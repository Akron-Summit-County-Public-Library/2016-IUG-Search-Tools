<?php namespace Database;

use GuzzleHttp\Client;
use Configuration\Configuration;
use Database\Amazon\AmazonSearch;

class Amazon
{
    protected $server;
    protected $client;

    public function __construct(Configuration $configuration, array $http_options = array())
    {
        $this->client = new Client($http_options);

        $config = (array)$configuration->config();
        if (isset($config['amazon']) === false) {
            return $this;
        }

        $config = (array)$config['amazon'];
        if (isset($config['server']) === false) {
            return $this;
        }

        $this->server = $config['server'];
    }

    public function search(AmazonSearch $search)
    {
        $search = $search->withEndpoint($this->server);

        return $this->client->request(
            $search->endpoint()->method,
            $search->url(),
            array()
        );
    }
}
