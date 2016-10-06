<?php namespace Database\Amazon;

trait AmazonRequest
{
    protected $endpoint = array(
        'method' => 'GET',
        'protocol' => 'http',
        'domain' => 'webservices.amazon.com',
        'uri' => '/onca/xml'
    );

    public function endpoint()
    {
        return (object)$this->endpoint;
    }

    public function parameters($timestamp) { return array(); }

    public function withAccessToken(AccessToken $token)
    {
        $changed = clone $this;
        $changed->setAccessToken($token);
        return $changed;
    }

    public function withEndpoint($url, $method='GET')
    {
        $changed = clone $this;

        $lowercase = strtolower($url);
        $changed->endpoint['method'] = strtoupper($method);
        $changed->endpoint['protocol'] = (substr($url, 0, 5) === 'https')
            ? 'https'
            : 'http';

        $lowercase = str_replace(['https://', 'http://'], '', $lowercase);

        $parts = explode('/', $lowercase);
        $changed->endpoint['domain'] = array_shift($parts);
        $changed->endpoint['uri'] = '/' . implode('/', $parts);

        return $changed;
    }

    public function queryString($parameters)
    {
        return http_build_query($parameters, null, '&', PHP_QUERY_RFC3986);
    }

    public function unsignedMessage($method, $domain, $uri, $query)
    {
        $message = array();
        $message[] = $method;
        $message[] = $domain;
        $message[] = $uri;
        $message[] = $query;

        return implode("\n", $message);
    }

    public function signature($unsigned_message)
    {
        $secret_key = $this->secretKey();
        $signed_message = hash_hmac(
            'sha256',
            $unsigned_message,
            $secret_key,
            true
        );

        $signed_message = base64_encode($signed_message);
        return rawurlencode($signed_message);
    }

    public function queryParameters()
    {
        $timestamp = $this->timestamp();
        $parameters = $this->parameters($timestamp);
        $query = $this->queryString($parameters);

        $endpoint = $this->endpoint();
        $domain = $endpoint->domain;
        $uri = $endpoint->uri;
        $method = $endpoint->method;

        $unsigned_message = $this->unsignedMessage(
            $method,
            $domain,
            (substr($uri, 0, 1) == '/') ? $uri : "/{$uri}",
            $query
        );

        $parameters['Signature'] = $this->signature($unsigned_message);

        return $parameters;
    }

    public function url()
    {
        if ($this->accessToken() === null) {
            throw new \InvalidArgumentException(
                'Please provide an AwsAccessToken via '.static::class.'::withAccessToken()'
            );
        }

        $timestamp = $this->timestamp();
        $parameters = $this->parameters($timestamp);
        $query = $this->queryString($parameters);

        $endpoint = $this->endpoint();
        $domain = $endpoint->domain;
        $uri = $endpoint->uri;

        $unsigned_message = $this->unsignedMessage(
            $endpoint->method,
            $domain,
            $uri,
            $query
        );

        $signature = $this->signature($unsigned_message);

        $protocol = $endpoint->protocol;
        return "{$protocol}://{$domain}{$uri}?{$query}&Signature={$signature}";
    }

    protected function setAccessToken(AccessToken $token)
    {
        $this->access_token = $token;
    }
    protected function accessToken()
    {
        return $this->access_token;
    }

    protected function publicKey()
    {
        return $this->accessToken()->publicKey();
    }
    protected function secretKey()
    {
        return $this->accessToken()->secretKey();
    }
    protected function userId()
    {
        return $this->accessToken()->userId();
    }

    protected function timestamp($time_string=null)
    {
        $timestamp = new \DateTimeImmutable(
            $time_string,
            new \DateTimeZone('UTC')
        );

        return $timestamp->format('Y-m-d\TH:i:s\Z');
    }
}
