<?php namespace Database\Rest;

use Database\Rest;
use Sierra\DataType\AccessToken;

class FetchAccessTokenAction
{
    private $client;
    private $token;

    public function __invoke(Rest $client, AccessToken $old_token)
    {
        $this->client = $client;

        if ($old_token->expired()) {
            return $this->fetchToken();
        }

        return (object)array(
            'token' => (string)$old_token,
            'timestamp' => $old_token->created(),
            'ttl' => $old_token->ttl()
        );
    }

    private function fetchToken()
    {
        $api_response = $this->query();

        $deserialize = $this->deserialize($api_response);

        return (object)array(
            'token' => $deserialize->access_token,
            'timestamp' => time(),
            'ttl' => $deserialize->expires_in
        );
    }

    private function query()
    {
        $authentication = $this->client->authentication();

        return $this->client->request(
            'POST',
            'token',
            array(
                'headers' => [
                    'Authorization' => "Basic {$authentication}",
                    'Content-Type' => 'application/x-www-form-urlencoded'
                ],
                'form_params' => [
                    'grant_type' => 'client_credentials'
                ]
            )
        );
    }

    private function deserialize($api_response)
    {
        return (object)json_decode($api_response->getBody());
    }
}
