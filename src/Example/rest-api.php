<?php require_once __DIR__ . '/../../vendor/autoload.php';

use Configuration\Configuration;
use Database\Rest;
use Database\Rest\FetchAccessToken;
use Database\Rest\FetchAccessTokenAction;
use Database\Rest\FetchAccessTokenResponse;

//--- Configure API connection ---//
$config = new Configuration;
$config->forceConfigFile('config');

//--- Prepare API connection ---//
$client = new Rest($config);

//--- Refresh or reuse access token ---//
$command = new FetchAccessToken(
    new FetchAccessTokenAction,
    new FetchAccessTokenResponse
);
$access = $command->__invoke($client);

//--- Use access token to query API ---//
$response = $client->query(
    $access->token(),
    'GET',
    'bibs',
    array(
        'query' => array(
            'deleted' => 'false',
            'suppressed' => 'false',
            'createdDate' => '[2013-07-30T19:20:28Z,]',
            'fields' => 'title,author,publishYear'
        )
    )
    /** For POST / PUT
    array(
        'json' => array(
            'deleted' => 'false',
            'suppressed' => 'false',
            'createdDate' => '[2013-07-30T19:20:28Z,]',
            'fields' => 'title,author,publishYear'
        )
    )*/
);

$content = $response->getBody();
$json = json_decode($content);

var_dump($json);
var_dump(array_keys(
    get_object_vars($json)
));
