<?php
declare(strict_types=1);
namespace PromoxApiClient\Nodes\App\Service;

use GuzzleHttp\Client;
use GuzzleHttp\Cookie\CookieJar;
use PromoxApiClient\Commons\Domain\Models\Connection;
use PromoxApiClient\Commons\GFunctions;

final class Node
{
    use GFunctions;

    private CookieJar $cookieJar;
    private  Connection $connection;

    public function __construct(CookieJar $cookieJar, Connection $connection)
    {
        $this->cookieJar = $cookieJar;
        $this->connection = $connection;
    }

    public function __invoke(): array
    {
        $client = new Client([$this->connection->getHost()]);
        $response = $client->get($this->connection->getUri() . "/nodes", ['verify' => false, 'exceptions' => false, 'cookies' => $this->cookieJar]);
        return $this->decodeBody($response);
    }

    public function version()
    {
        $client = new Client([$this->connection->getHost()]);
        $response = $client->get($this->connection->getUri() . "/nodes", ['verify' => false, 'exceptions' => false, 'cookies' => $this->cookieJar]);
        return $this->decodeBody($response);
    }
}