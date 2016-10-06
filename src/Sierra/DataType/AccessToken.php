<?php namespace Sierra\DataType;

class AccessToken
{
    private $value = '';
    private $timestamp = 0;
    private $time_to_live = 0;

    public function __construct($value = '', $timestamp = 0, $expires_in = 3600)
    {
        $this->value = $value;
        $this->timestamp = $timestamp;
        $this->time_to_live = $expires_in;

        //--- Fetch previously used if not expired, default value given ---//
        $this->fetchKey();

        //--- Whether using cache or non-default value argument, save to file for next run ---//
        $this->storeKey();
    }

    public function expired()
    {
        $timestamp = $this->created();
        $ttl = $this->ttl();

        return $this->guardExpiration($timestamp, $ttl);
    }

    public function created()
    {
        return $this->timestamp;
    }

    public function ttl()
    {
        return $this->time_to_live;
    }

    public function __toString()
    {
        return $this->value;
    }

    public function toString()
    {
        return $this->value;
    }

    private function fetchKey()
    {
        //--- Retrieve previous token from access.key file cache ---//
        $file = file_exists('access.key');
        if (! $file) {
            return;
        }

        //--- File cache is comma-separated values ---//
        $file_content = file_get_contents('access.key');
        list($timestamp, $expiration, $value) = explode(',', $file_content);

        $this->remember($timestamp, $expiration, $value);
    }

    private function storeKey()
    {
        //--- Only overwrite if token was successfully obtained ---//
        if ($this->toString() === '') {
            return;
        }

        //--- Only overwrite if currently used token is expired ---//
        if ($this->expired()) {
            return;
        }

        //--- Overwrite cached with comma-seperated values --//
        $values = array(
            $this->timestamp,
            $this->time_to_live,
            $this->value
        );

        $comma_separated = implode(',', $values);
        file_put_contents('access.key', $comma_separated);
    }

    private function remember($cached_timestamp, $cached_expiration, $cached_value)
    {
        //--- Only use previously used token if it's still valid ---//
        $has_expired = $this->guardExpiration(
            $cached_timestamp,
            $cached_expiration
        );

        if ($has_expired) {
            return;
        }

        $this->value = $cached_value;
        $this->timestamp = $cached_timestamp;
        $this->time_to_live = $cached_expiration;
    }

    private function guardExpiration($timestamp, $ttl)
    {
        //--- Cache expiration based on Unix timestamp & time-to-live (seconds) ---//
        $new = time();

        $difference = abs($new - $timestamp);
        return ($difference >= $ttl);
    }
}
