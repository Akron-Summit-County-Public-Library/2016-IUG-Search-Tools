<?php namespace Database\Rest;

use Database\Rest;
use Sierra\DataType\AccessToken;

class FetchAccessToken
{
    public function __construct(
        FetchAccessTokenAction $action, FetchAccessTokenResponse $response
    )
    {
        $this->action = $action;
        $this->response = $response;
    }

    public function __invoke(Rest $client)
    {
        $cached_token = $this->response->token();

        $stored = $this->action->__invoke($client, $cached_token);

        return $this->response->withAccessToken(
            new AccessToken(
                $stored->token,
                $stored->timestamp,
                $stored->ttl
            )
        );
    }
}
