<?php
declare(strict_types=1);
namespace Ginernet\Proxmox\Tests;

use Ginernet\Proxmox\Cpus\Domain\Exceptions\CpuNotFound;
use Ginernet\Proxmox\Cpus\Domain\Reponses\CpusResponse;
use Ginernet\Proxmox\VM\Domain\Exceptions\VmErrorCreate;
use Ginernet\Proxmox\VM\Domain\Responses\VmsResponse;
use PHPUnit\Framework\TestCase;
use Ginernet\Proxmox\Auth\Domain\Responses\LoginResponse;
use Ginernet\Proxmox\Commons\Domain\Exceptions\AuthFailedException;
use Ginernet\Proxmox\Commons\Domain\Exceptions\HostUnreachableException;
use Ginernet\Proxmox\GClient;
use Ginernet\Proxmox\Networks\Domain\Exceptions\NetworksNotFound;
use Ginernet\Proxmox\Networks\Domain\Responses\NetworksResponse;
use Ginernet\Proxmox\Nodes\Domain\Responses\NodesResponse;
use Ginernet\Proxmox\Storages\Domain\Exceptions\StoragesNotFound;
use Ginernet\Proxmox\Storages\Domain\Responses\StoragesResponse;

class GClientTest extends  TestCase
{

    private LoginResponse $auth;
    private GClient $client;

    public function setUp():void{
        $this->client = new GClient($_ENV['HOST'],$_ENV['USERNAME'],$_ENV['PASSWORD'],$_ENV['REALM']);
        $this->auth = $this->client->login();
    }

    public function testLoginClientOk():void
    {
        $this->assertInstanceOf(LoginResponse::class, $this->auth);
    }

    public function testLoginClientUserNameKO():void
    {
        $client = new GClient($_ENV['HOST'],'BRABRA',$_ENV['PASSWORD'],$_ENV['REALM']);
        $result = $client->login();
        $this->assertInstanceOf(AuthFailedException::class, $result);
    }

    public function testLoginClientPASSWORDKO():void
    {
        $client = new GClient($_ENV['HOST'],$_ENV['USERNAME'],'DFDFDF',$_ENV['REALM']);
        $result = $client->login();
        $this->assertInstanceOf(AuthFailedException::class, $result);
    }

    public function testLoginClientREALMKO():void
    {
        $client = new GClient($_ENV['HOST'],$_ENV['USERNAME'],$_ENV['PASSWORD'],'BRA');
        $result = $client->login();
        $this->assertInstanceOf(AuthFailedException::class, $result);
    }

    public function testLoginClientHOSTKO():void
    {
        $client = new GClient('bbbb',$_ENV['USERNAME'],$_ENV['PASSWORD'],$_ENV['REALM']);
        $result = $client->login();
        $this->assertInstanceOf(HostUnreachableException::class, $result);
    }

    public function testGetNodesOK():void
    {
        $result = $this->client->GetNodes();
        $this->assertInstanceOf(NodesResponse::class, $result);
    }

    public function testGetStoragesFromNodeOK():void
    {
        $result = $this->client->GetStoragesFromNode("ns1000");
        $this->assertInstanceOf(StoragesResponse::class, $result);
    }


        public function testGetStoragesFromNodeKO():void
    {
        $result = $this->client->GetStoragesFromNode("test");
        $this->assertInstanceOf(StoragesNotFound::class, $result);

    }

    public  function testGetNeworkFromNodeOK():void
    {
        $result = $this->client->GetNetworksFromNode("ns1000");
        $this->assertInstanceOf(NetworksResponse::class, $result);
    }

    public  function testGetNeworkFromNodeKO():void
    {
        $result = $this->client->GetNetworksFromNode("test");
        $this->assertInstanceOf(NetworksNotFound::class, $result);
    }

    public function testGetCpusFromNodeOK():void
    {
        $result = $this->client->GetCpusFromNode("ns1000");
        $this->assertInstanceOf(CpusResponse::class, $result);
    }

   /* public  function testGetCpusFromNodeKO():void
    {
        $result = $this->client->GetCpusFromNode("t");
        var_dump($result);
        $this->assertInstanceOf(CpuNotFound::class, $result);
    }
    */


    public function testCreateVMOk():void
    {
        $result =$this->client->createVM('ns1000', 102,2,'Prueba', 0, 'virtio','vmbr0');
        $this->assertInstanceOf(VmsResponse::class, $result);

    }
    public function testCreateVMError():void
    {
        $result =$this->client->createVM('ns1000', 102,2,'Prueba', 0, 'virtio','vmbr0');
        $this->assertInstanceOf(VmErrorCreate::class, $result);

    }


}