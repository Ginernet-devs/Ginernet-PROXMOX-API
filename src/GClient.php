<?php
declare(strict_types=1);
namespace PromoxApiClient;
use GuzzleHttp\Cookie\CookieJar;
use PromoxApiClient\Auth\App\Service\Login;
use PromoxApiClient\Auth\Domain\Responses\LoginResponse;

class GClient
{
    private string $hostname;
    private int $port;
    private string $username;
    private string $password;
    private string $realm;
    private string $CSRFPreventionToken;
    private CookieJar $cookie;

   public function __construct($hostname, $username, $password, $realm, $port = 8006) {
        $this->hostname = $hostname;
        $this->port = $port;
        $this->username = $username;
        $this->password = $password;
        $this->realm = $realm;
    }

    public function login():LoginResponse{
        $auth = new Login($this->hostname, $this->username, $this->password, $this->realm, $this->port);
        return $auth();
    }

}