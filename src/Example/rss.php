<?php require_once __DIR__ . '/../../vendor/autoload.php';

use Database\LegacyRss;
use View\Json;
use View\Rss;
use View\Csv;

//--- Open example file ---//
$contents = file_get_contents('http://www.quotationspage.com/data/qotd.rss');

$db = new LegacyRss($contents);

// To PHP array: $db->feed()
$items = $db->feed();

// To JSON File: (new Json)->__invoke($db->feed())->toFile('output');
//echo (new Json)->__invoke($items)->toString();
//----

// To RSS File: (new Rss)->__invoke($db->feed(), 'http://localhost', $db->metadata())->toFile('output');
echo (new Rss)->__invoke($items, 'http://localhost', $db->metadata())->toString();
//----

// To CSV File: (new Csv(false, true))->__invoke($db->feed())->toFile('output');
// echo (new Csv)->__invoke($items)->toString();
//----

