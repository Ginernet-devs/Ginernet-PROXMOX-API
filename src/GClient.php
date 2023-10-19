<?php
declare(strict_types=1);
namespace PromoxApiClient;

class GClient
{
    private string $hostname;
    private int $port;
    private string $username;
    private string $password;
    private string $realm;

    public function __construct($hostname, $username, $password, $realm, $port = 8006) {
        $this->hostname = $hostname;
        $this->port = $port;
        $this->username = $username;
        $this->password = $password;
        $this->realm = $realm;

    }

}