<?php
declare(strict_types=1);
namespace PromoxApiClient\Auth\App\Service;

use GuzzleHttp\Client;
use GuzzleHttp\Cookie\CookieJar;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Psr7\Response;
use PromoxApiClient\Auth\Domain\Exceptions\AuthFailedException;
use PromoxApiClient\Auth\Domain\Exceptions\HostUnreachableException;
use PromoxApiClient\Auth\Domain\Responses\LoginResponse;

final class Login
{
    private string $hostname;
    private int $port;
    private string $username;
    private string $password;
    private string $realm;
    private String $ticket;

    private array $defaultHeaders = [
        'Content-Type' => 'application/json',
        'Accept' => 'application/json',
    ];

    public function __construct($hostname, $username, $password, $realm, $port)
    {
        $this->hostname = $hostname;
        $this->port = $port;
        $this->username = $username;
        $this->password = $password;
        $this->realm = $realm;
    }

    public function __invoke():?LoginResponse
    {
      $client = new Client([$this->hostname]);
      try{
        $response = $client->request('POST',$this->hostname.":".$this->port."/api2/json/access/ticket",['https_errors'=>false,
            'verify' => false,
            'headers' => $this->defaultHeaders,
            'json'=>['username'=>$this->username, 'password'=>$this->password,'realm'=>$this->realm]]);
             $data = $this->decodeBody($response);
             $cookie =  $this->getCookies($data['ticket']);
             return new LoginResponse($data['CSRFPreventionToken'],$cookie,$data['ticket']);
        }catch (GuzzleException $ex){
            if ($ex->getCode() === 401) throw new AuthFailedException();
            if ($ex->getCode() === 0) throw new HostUnreachableException();
      }
      return null;
    }
    private function  getCookies(String $ticket):CookieJar
    {
        return CookieJar::fromArray(
            ['PVEAuthCookie' => $ticket],$this->hostname);
    }
    public function decodeBody(Response $data):array{
        return json_decode($data->getBody()->getContents(), true)['data'];
    }
}