<?php namespace Database\Amazon;

use Configuration\Configuration;

class AccessToken
{
    protected $client_key = '';
    protected $client_secret = '';

    protected $affiliate_id = '';

    public static function create($client_key, $client_secret, $affiliate_id)
    {
        $token = new static;

        $token->setPublicKey($client_key);
        $token->setSecretKey($client_secret);
        $token->setAffiliateId($affiliate_id);

        return $token;
    }

    public static function fromConfiguration(Configuration $credentials)
    {
        $api_options = (array)$credentials->config()->amazon;

        $token = new static;
        $token->initialize($api_options);
        return $token;
    }

    private function initialize(array $config)
    {
        foreach ($config as $key => $value) {
            if (! property_exists($this, $key)) {
                continue;
            }

            $this->$key = $value;
        }
    }

    public function publicKey()
    {
        return $this->client_key;
    }
    public function secretKey()
    {
        return $this->client_secret;
    }
    public function userId()
    {
        return $this->affiliate_id;
    }

    protected function setPublicKey($key)
    {
        $this->client_key = $key;
    }

    protected function setSecretKey($key)
    {
        $this->client_secret = $key;
    }

    protected function setAffiliateId($id)
    {
        $this->affiliate_id = $id;
    }
}
