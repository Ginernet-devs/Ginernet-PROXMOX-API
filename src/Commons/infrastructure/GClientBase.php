<?php
declare(strict_types=1);
namespace Ginernet\Proxmox\Commons\infrastructure;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Ginernet\Proxmox\Commons\Application\Helpers\GFunctions;
use Ginernet\Proxmox\Commons\Domain\Entities\Connection;
use Ginernet\Proxmox\Commons\Domain\Exceptions\AuthFailedException;
use Ginernet\Proxmox\Commons\Domain\Exceptions\HostUnreachableException;
use Ginernet\Proxmox\Commons\Domain\Models\CoockiesPVE;

abstract class GClientBase
{

    use GFunctions;
    private Client $client;
    private Connection $connection;
    private CoockiesPVE $cookies;

    private array $defaultHeaders = [
        'Content-Type' => 'application/json',
        'Accept' => 'application/json',
    ];
    public function __construct(Connection $connection, CoockiesPVE $cookies)
    {
        $this->cookies= $cookies;
        $this->connection = $connection;
        $this->client = new Client([$connection->getHost()]);
    }
    protected function Get(string $request, array $params=[]):?array
    {
       try{
           $result= $this->client->request('GET', $this->connection->getUri().$request,[
              'https_errors' => false,
              'verify'=> false,
               'headers'=>array_merge($this->defaultHeaders,['CSRFPreventionToken'=>$this->cookies->getCSRFPreventionToken()]),
               'query' =>$params,
               'exceptions'=>false,
               'cookies'=>$this->cookies->getCookies(),
           ]);
           return $this->decodeBody($result);
       }catch (GuzzleException $ex){
           if ($ex->getCode() === 0) throw new HostUnreachableException();
           if ($ex->getCode() === 401) throw new AuthFailedException();
       }
       return null;

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
           if ($ex->getCode() === 401) throw new AuthFailedException();
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