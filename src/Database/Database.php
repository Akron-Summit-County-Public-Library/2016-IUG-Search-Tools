<?php namespace Database;

use Configuration\Configuration;

class Database
{
    /** @var PDO */
    private $dbh;

    public function __construct(Configuration $config)
    {
        $db_config = (object)$config->config()->db;

        $this->initialize($db_config);
    }

    public function get()
    {
        return $this->dbh;
    }

    protected function initialize($db)
    {
        $driver = $db->driver;
        $host = $db->host;
        $port = $db->port;
        $username = $db->user;
        $password = $db->password;
        $ssl = ($db->ssl) ? ';sslmode=require' : '';
        $db = $db->database;

        $this->dbh = new \PDO(
            "{$driver}:host={$host};port={$port};dbname={$db}{$ssl}",
            $username,
            $password
        );
    }
}
