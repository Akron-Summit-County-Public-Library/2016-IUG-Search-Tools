<?php namespace Database\Rest;

use Sierra\DataType\AccessToken;

class FetchAccessTokenResponse
{
    private $token;

    public function __construct(AccessToken $token = null)
    {
        $this->token = (is_null($token))
            ? new AccessToken
            : $token;
    }

    public function withAccessToken(AccessToken $token)
    {
        return new static($token);
    }

    public function __call($method_name, $method_arguments)
    {
        if (property_exists($this, $method_name)) {
            return $this->$method_name;
        }

        throw new \BadMethodCallException(sprintf(
            '%s::%s() called, but no method named %s exists on class',
            get_class($this),
            $method_name,
            $method_name
        ));
    }
}
