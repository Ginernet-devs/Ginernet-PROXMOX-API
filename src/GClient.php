<?php
declare(strict_types=1);
namespace PromoxApiClient;
use GuzzleHttp\Cookie\CookieJar;
use PromoxApiClient\Auth\App\Service\Login;
use PromoxApiClient\Auth\Domain\Responses\LoginResponse;
use PromoxApiClient\Commons\Domain\Entities\Connection;
use PromoxApiClient\Commons\Domain\Entities\CookiesPVE;
use PromoxApiClient\Commons\Domain\Exceptions\AuthFailedException;
use PromoxApiClient\Commons\Domain\Exceptions\HostUnreachableException;
use PromoxApiClient\Nodes\App\Service\Node;

class GClient
{
    private Connection $connection;
    private string $CSRFPreventionToken;
    private CookiesPVE $cookiesPVE;

   public function __construct($hostname, $username, $password, $realm, $port = 8006)
   {
       $this->connection = new Connection($hostname, $port,$username,$password,$realm);
   }

    public function login():LoginResponse|AuthFailedException|HostUnreachableException{
       try {
           $auth = new Login($this->connection, null);
           $result= $auth();
           $this->cookiesPVE= new CookiesPVE($result->getCSRFPreventionToken(),$result->getCookies(),$result->getTicket());
           return $result;
       }catch(AuthFailedException $ex){
           return new AuthFailedException();
       }catch (HostUnreachableException $ex){
           return new HostUnreachableException();
       }
    }

    public  function GetNodes():array
    {
       $nodes= new Node($this->connection, $this->cookiesPVE);
        return $nodes();
    }
}