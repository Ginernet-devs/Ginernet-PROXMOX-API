<?php
declare(strict_types=1);
namespace PromoxApiClient\Commons\infrastructure;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use PromoxApiClient\Commons\Domain\Exceptions\HostUnreachableException;
use PromoxApiClient\Commons\Domain\Models\Connection;
use PromoxApiClient\Commons\GFunctions;

abstract class GClientBase
{

    use GFunctions;
    private Client $client;
    private Connection $connection;

    private array $defaultHeaders = [
        'Content-Type' => 'application/json',
        'Accept' => 'application/json',
    ];
    public function __construct(Connection $connection)
    {

        $this->connection = $connection;
        $this->client = new Client([$connection->getHost()]);
    }
    protected function Get():?array
    {
       return  null;

    }

    protected function Post(string $request, array $requestBody): ?array
    {
       try {
            $result=  $this->client->request("POST", $this->connection->getUri() .  $request, [
                'https_errors'=>false,
                'verify' => false,
                'headers' => $this->defaultHeaders,
                'json' => (count($requestBody) > 0 ) ? $requestBody : null]);
           return $this->decodeBody($result);
        }catch (GuzzleException $ex){
           if ($ex->getCode() === 0) throw new HostUnreachableException();
        }
      return null;
    }

    protected function getClient():Client{
        return $this->client;
    }
    public function getConnection():Connection{
        return $this->connection;
    }

}