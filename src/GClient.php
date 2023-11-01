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
use PromoxApiClient\Networks\App\Service\GetNetworksFromNode;
use PromoxApiClient\Networks\Domain\Exceptions\NetworksNotFound;
use PromoxApiClient\Networks\Domain\Responses\NetworksResponse;
use PromoxApiClient\Nodes\App\Service\GetNode;
use PromoxApiClient\Nodes\App\Service\GetNodes;
use PromoxApiClient\Nodes\Domain\Responses\NodesResponse;
use PromoxApiClient\Storages\App\Service\GetStoragesFromNode;
use PromoxApiClient\Storages\Domain\Exceptions\StoragesNotFound;
use PromoxApiClient\Storages\Domain\Responses\StoragesResponse;

class GClient
{
    private Connection $connection;
    private string $CSRFPreventionToken;
    private CookiesPVE $cookiesPVE;

    public function __construct($hostname, $username, $password, $realm, $port = 8006)
    {
        $this->connection = new Connection($hostname, $port, $username, $password, $realm);
    }

    public function login(): LoginResponse|AuthFailedException|HostUnreachableException
    {
        try {
            $auth = new Login($this->connection, null);
            $result = $auth();
            $this->cookiesPVE = new CookiesPVE($result->getCSRFPreventionToken(), $result->getCookies(), $result->getTicket());
            return $result;
        } catch (AuthFailedException $ex) {
            return new AuthFailedException();
        } catch (HostUnreachableException $ex) {
            return new HostUnreachableException();
        }
    }

    public function GetNodes(): NodesResponse|AuthFailedException|HostUnreachableException
    {
        try {
            $nodes = new GetNodes($this->connection, $this->cookiesPVE);
            return $nodes();
        }catch (AuthFailedException $ex) {
            return new AuthFailedException();
        } catch (HostUnreachableException $ex) {
            return new HostUnreachableException();
        }
    }

    public function GetStoragesFromNode(string $node):StoragesResponse |AuthFailedException|HostUnreachableException|StoragesNotFound
    {
        try {
            $storages = new GetStoragesFromNode($this->connection, $this->cookiesPVE);
            return $storages($node);
        }catch (AuthFailedException $ex) {
            return new AuthFailedException();
        }catch (HostUnreachableException $ex) {
            return new HostUnreachableException();
        }catch (StoragesNotFound $ex){
            return new StoragesNotFound();
        }
    }

    public function GetNetworksFromNode(string $node):NetworksResponse|AuthFailedException|HostUnreachableException|NetworksNotFound
    {
        try {
            $networks = new GetNetworksFromNode($this->connection, $this->cookiesPVE);
            return $networks($node);
        }catch (AuthFailedException $ex){
            return new AuthFailedException();
        }catch(HostUnreachableException $ex){
            return new HostUnreachableException();
        }catch(NetworksNotFound $ex){
            return  new NetworksNotFound();
        }

    }

}