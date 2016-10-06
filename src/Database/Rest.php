<?php namespace Database;

use GuzzleHttp\Client;
use Configuration\Configuration;
use Sierra\DataType\AccessToken;

class Rest
{
    protected $client;
    protected $options = array(
        'base_uri' => ''
    );

    protected $credentials = array(
        'client_key' => '',
        'client_secret' => ''
    );

    public function __construct(Configuration $credentials, array $http_options = array())
    {
        $api_options = (array)$credentials->config()->api;

        $this->initialize($api_options, $http_options);
    }

    public function authentication()
    {
        $credentials = array_values($this->credentials);
        $authentication = implode(':', $credentials);

        return base64_encode($authentication);
    }

    public function query(AccessToken $access_token, $method, $endpoint, array $options = array())
    {
        $default = array('headers' => array(
            'Authorization' => "Bearer {$access_token}"
        ));

        $options = array_merge_recursive($default, $options);

        return $this->request($method, $endpoint, $options);
    }

    public function request($method, $endpoint, array $options = array())
    {
        return $this->client->request($method, $endpoint, $options);
    }

    private function initialize(array $api_config, array $http_options)
    {
        $this->guardBaseUri($api_config, 'server', 'https://httpbin.org/get/');

        $this->initializeApiConfig($api_config);
        $this->initializeHttpConfig($http_options);

        $this->createClient();
    }

    private function guardBaseUri($api_config, $input, $default)
    {
        $this->options['base_uri'] = (isset($api_config[$input]))
            ? $api_config[$input]
            : $default;
    }

    private function initializeApiConfig($api_config)
    {
        foreach ($api_config as $key => $value) {
            if (! array_key_exists($key, $this->credentials)) {
                continue;
            }

            $this->credentials[$key] = $value;
        }
    }

    private function initializeHttpConfig($http_options)
    {
        foreach ($http_options as $key => $value) {
            if (! array_key_exists($key, $this->options)) {
                continue;
            }

            $this->options[$key] = $value;
        }
    }

    protected function createClient()
    {
        $options = $this->options;

        $this->client = new Client($options);
    }
}
