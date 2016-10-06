<?php require_once __DIR__ . '/../../vendor/autoload.php';

use Configuration\Configuration;

use Process\Filter\FilterRequest;
use Process\Filter\FilterResponse;

use Process\Filter\Strategy\PatronIsAdultAtCheckout;
use Process\Filter\Strategy\FineHasPastStatuteDebt;
use Process\Filter\Strategy\NoFilter;

use View\Json;
use View\Csv;

function longOverdueReport($input_file='output.json', $strategy=false, $user_file_name='adult')
{
    if (!$strategy) {
        $strategy = new PatronIsAdultAtCheckout;
    }

    //--- Specifications ---//
    $strategy = $strategy->and(new FineHasPastStatuteDebt);

    //--- Configure filter process ---//
    $request = new FilterRequest($strategy, 'patron_record_id');
    $response = new FilterResponse($request);

    //--- Filter the JSON dump ---//
    $response->__invoke($input_file);

    $results = $response->results();
    // $unique_results = $response->uniqueResults();
    $count_unique = $response->countUniqueResults();
    $count = $response->countResults();

    $output = array();
    foreach ($results as $row)
    {
        $barcode = $row->patron_barcode;
        if (! $barcode) {
            continue;
        }

        if (isset($output[$barcode]) === false) {
            $output[$barcode] = array();
        }

        if (isset($output[$barcode]['debt']) === false) {
            $output[$barcode]['debt'] = 0.00;
        }

        if (isset($output[$barcode]['items']) === false) {
            $output[$barcode]['items'] = 0;
        }

        if ($row->past_statute_debt > 0) {
            $output[$barcode]['debt'] += $row->past_statute_debt;
            $output[$barcode]['items']++;
        }
    }

    $temp = $output;
    $output = array();

    foreach ($temp as $barcode => $info)
    {
        $output[] = array_merge(array('barcode' => $barcode), $info);
    }

    //--- Render results ---//
    $json_view = new Json;
    $csv_view = new Csv($http_attachment = false, $include_headers = true);

    $timestamp = strftime('%Y-%m-%d-%H%M%S');
    $file_name = "{$timestamp}_{$user_file_name}-long-overdue-report";

    $csv_view->__invoke($output)->toFile($file_name);
}

$file_name = isset($argv[1]) ? $argv[1] : 'output.json';

longOverdueReport($file_name, new PatronIsAdultAtCheckout, 'adults');
longOverdueReport($file_name, (new PatronIsAdultAtCheckout)->not(), 'minors');
