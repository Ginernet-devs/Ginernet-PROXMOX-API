<?php
declare(strict_types=1);
namespace Ginernet\Proxmox\Commons\Domain\Entities;

final class Connection
{

    private string $host;
    private int $port;
    private string $username;
    private string $password;
    private string $realm;



    private string $uri;

    /**
     * @param string $host
     * @param int $port
     * @param string $username
     * @param string $password
     * @param string $uri
     * @param string $realm
     */
    public function __construct(string $host, int $port, string $username, string $password, string $realm)
    {
        $this->host = $host;
        $this->port = $port;
        $this->username = $username;
        $this->password = $password;
        $this->realm = $realm;
        $this->uri = "https://".$host.":".$port."/api2/json/";
    }

    public function getHost(): string
    {
        return $this->host;
    }

    public function getPort(): int
    {
        return $this->port;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function getUri(): string
    {
        return $this->uri;
    }

    public function getRealm():string
    {
        return $this->realm;
    }
}