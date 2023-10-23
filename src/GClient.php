<?php
declare(strict_types=1);
namespace PromoxApiClient;
use GuzzleHttp\Cookie\CookieJar;
use PromoxApiClient\Auth\App\Service\Login;
use PromoxApiClient\Auth\Domain\Responses\LoginResponse;
use PromoxApiClient\Commons\Domain\Exceptions\AuthFailedException;
use PromoxApiClient\Commons\Domain\Exceptions\HostUnreachableException;
use PromoxApiClient\Commons\Domain\Models\Connection;
use PromoxApiClient\Nodes\App\Service\Node;

class GClient
{
    private Connection $connection;
       private string $CSRFPreventionToken;
    private CookieJar $cookie;

   public function __construct($hostname, $username, $password, $realm, $port = 8006)
   {
       $this->connection = new Connection($hostname, $port,$username,$password,$realm);
   }

    public function login():LoginResponse|AuthFailedException|HostUnreachableException{
       try {
           $auth = new Login($this->connection);
           return $auth();
       }catch(AuthFailedException $ex){
           return new AuthFailedException();
       }catch (HostUnreachableException $ex){
           return new HostUnreachableException();
       }
    }

    public  function GetNodes(CookieJar $cookieJar):array
    {
       $nodes= new Node($cookieJar , $this->connection);
        return $nodes();
    }
}