<?php require_once __DIR__ . '/../../vendor/autoload.php';

use View\Json;
use View\Csv;

use Configuration\Configuration;
use Database\Database;
use Database\SqlQuery;

use Sierra\Command\Sql\Pagination\PaginatedQueryCommand;
use Sierra\Command\Sql\Pagination\PaginatedQueryHandler;

//--- Dynamic content ---//
$template = array();
$template['today'] = strftime('%Y-%m-%d');
$template['statute'] = strftime('%Y-%m-%d', strtotime('-6 years'));

//--- Configure database connection ---//
$config = new Configuration;
$config->forceConfigFile('config');

//--- Prepare database connection ---//
$database = new Database($config);

//--- Format SQL ---//
$command = (new PaginatedQueryCommand)->setPage(1)->setLimit(1000);
$handler = new PaginatedQueryHandler($database, $command);

//--- Determine SQL Query ---//
$sql_file_name = '';
if (isset($argv[1])) {
    $sql_file_name = $argv[1];
}

if (! $sql_file_name) {
    throw new \InvalidArgumentException(sprintf(
        'Usage: php %s "SQL File Name", please see Queries folder for available search types',
        (isset($argv[0])) ? $argv[0] : './database.php'
    ));
}

//--- Fetch and Tokenize SQL Query ---//
$source_directory = realpath(__DIR__ . '/../Queries');

$directory = new SqlQuery($source_directory);
$sql = $directory->fetchFile($sql_file_name)->withContent($template);
if (! $sql) {
    throw new \InvalidArgumentException(sprintf(
        'No SQL file named "%s.sql" found. Please ensure the .sql file exists in %s',
        $sql_file_name,
        $source_directory
    ));
}

//--- Return SQL results ---//
$result = $handler->__invoke($sql)
                  ->result();

//--- Render results ---//
$file_name = strftime('%Y-%m-%d-%H%M%S');
$file_name .= strtolower("_{$sql_file_name}");
$file_name = str_replace(' ', '-', $file_name);

$csv_view->__invoke($result)->toFile($file_name);
$json_view->__invoke($result)->toFile($file_name);

$log = fopen('log.txt', 'a');
fwrite($log, time() . ': ' . $file_name . "\n");
fclose($log);
