<?php
declare(strict_types=1);
namespace PromoxApiClient\Auth\App\Service;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use PromoxApiClient\Auth\Domain\Responses\LoginResponse;
use PromoxApiClient\Commons\Application\Helpers\GFunctions;
use PromoxApiClient\Commons\Domain\Entities\Connection;
use PromoxApiClient\Commons\Domain\Entities\CookiesPVE;
use PromoxApiClient\Commons\Domain\Exceptions\AuthFailedException;
use PromoxApiClient\Commons\Domain\Exceptions\HostUnreachableException;
use PromoxApiClient\Commons\infrastructure\GClientBase;

final class Login
{
    use GFunctions;

    private string $ticket;

    private Connection $connection;
    private Client $client;

    private array $defaultHeaders = [
        'Content-Type' => 'application/json',
        'Accept' => 'application/json',
    ];

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
        $this->client = new Client([$connection->getHost()]);
    }

    public function __invoke(): ?LoginResponse

    {
        try {
            $body=[
                'username' => $this->connection->getUsername(),
                'password' => $this->connection->getPassword(),
                'realm' => $this->connection->getRealm()
            ];

            $result=  $this->client->request("POST", $this->connection->getUri() .'access/ticket' , [
                'https_errors'=>false,
                'verify' => false,
                'headers' => $this->defaultHeaders,
                'json' => (count($body) > 0 ) ? $body : null]);
           $response = $this->decodeBody($result);
           $cookie = $this->getCookies($response['ticket'], $this->connection->getHost());
           return new LoginResponse($response['CSRFPreventionToken'], $cookie, $response['ticket']);
        } catch (GuzzleException $ex) {
            if ($ex->getCode() === 401) throw new AuthFailedException();
            if ($ex->getCode() === 0) throw new HostUnreachableException();
        }
        return null;
    }


}
