<?php require_once __DIR__ . '/../../vendor/autoload.php';

use Configuration\Configuration;
use Database\Amazon\AccessToken;
use Database\Amazon\AmazonSearch;
use Database\Amazon;

//--- Configure API connection ---//
$config = new Configuration;
$config->forceConfigFile('config');

//--- Parse Private / Public Key ---//
$amazon_token = AccessToken::fromConfiguration($config);

//--- Prepare Request ---//
$search = array(
    'ISBN' => '076243631X',
    'Books' => true
);

$request = (new AmazonSearch($search))->withAccessToken($amazon_token);

//--- Send Request ---//
$response = (new Amazon($config))->search($request)->getBody();

echo $response;
