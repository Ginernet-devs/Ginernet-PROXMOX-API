<?php
declare(strict_types=1);
namespace Ginernet\Proxmox\Tests;

use Ginernet\Proxmox\Cluster\App\GetClusterStatus;
use Ginernet\Proxmox\Cluster\Domain\Responses\ClusterResponse;
use Ginernet\Proxmox\Cpus\Domain\Exceptions\CpuNotFound;
use Ginernet\Proxmox\Cpus\Domain\Reponses\CpusResponse;
use Ginernet\Proxmox\Proxmox\Version\Domain\Responses\VersionResponse;
use Ginernet\Proxmox\VM\Domain\Exceptions\ResizeVMDiskException;
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
    private LoginResponse $authCLuster;
    private GClient $client;
    private GClient $clientCluster;

    public function setUp():void{
        $this->client = new GClient($_ENV['HOST'],$_ENV['USERNAME'],$_ENV['PASSWORD'],$_ENV['REALM']);
        $this->auth = $this->client->login();
        $this->clientCluster = new GClient($_ENV['HOST_CLUSTER'], $_ENV['USERNAME_CLUSTER'], $_ENV['PASSWORD_CLUSTER'], $_ENV['REALM_CLUSTER']);
        $this->authCLuster = $this->clientCluster->login();
    }

    public function testLoginClientOk():void
    {
        $this->assertInstanceOf(LoginResponse::class, $this->auth);
    }
/*
    public function testLoginClientUserNameKO():void
    {

            $client = new GClient($_ENV['HOST_CLUSTER'], 'root', $_ENV['PASSWORD'], $_ENV['REALM']);
            $result = $client->login();
            $this->assertInstanceOf(AuthFailedException::class, $result);
            $this->assertEquals(401, $result->getCode());
    }

    public function testLoginClientPASSWORDKO():void
    {
        $client = new GClient($_ENV['HOST'],$_ENV['USERNAME'],'DFDFDF',$_ENV['REALM']);
        $result = $client->login();
        $this->assertInstanceOf(AuthFailedException::class, $result);
        $this->assertEquals(401, $result->getCode());
    }

    public function testLoginClientREALMKO():void
    {
        $client = new GClient($_ENV['HOST'],$_ENV['USERNAME'],$_ENV['PASSWORD'],'BRA');
        $result = $client->login();
        $this->assertInstanceOf(AuthFailedException::class, $result);
        $this->assertEquals(401, $result->getCode());
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
        $result =$this->client->createVM('ns1047', 101,2,'hostname', 0, 'virtio',
            'vmbr0',1,true, 'virtio-scsi-pci', 'SCSI',0, 'nvme', 'on','directsync','/mnt/pve/nfs-iso/gcp-images/Debian-12-x86_64-GridCP-PVE_KVM-20240610.qcow2',
            'deb12',0, 'nvme','scsi0', 1,0,'5.134.113.50/24','5.134.113.1','root', 'password', 'x86-64-v2-AES', 4096,0,
            'l26' ,'ovmf','pc-q35-8-1', 'nvme',1);
        $this->assertInstanceOf(VmsResponse::class, $result);

    }
    public function testCreateVMError():void
    {

        $result = $this->client->createVM('ns1047', 100, 2, 'ho', 0, 'virtio',
            'vmbr0',1, true, 'virtio-scsi-pci', 0, 0, 'on', 'directsync', '/mnt/pve/nfs-iso/gcp-images/Debian-12-x86_64-GridCP-PVE_KVM-20240610.qcow2',
            'deb12',0, 'nvme', 'scsi0', 'scsi0', '1', 0,'5.134.113.50/24','5.134.113.1','root','password','x86-64-v2-AES', 4096, 0 ,null,
            null,null, null,null);
        $this->assertInstanceOf(VmErrorCreate::class, $result);
    }

       /*    public function testConfigVM():void
            {
                $this->client->configVM('ns1000',102,0,'on','directsync','/image/images/000/Debian-12-x86_64-GridCP-PVE_KVM-20231012.qcow2');
            }
*/

    public function testResizeVMDiskOk():void
    {
        $result = $this->client->resizeVMDisk('ns1000', 102, 'scsi0','25G');
        $this->assertNotEmpty($result);
    }

    public function testResizeVMDiskKO():void
    {
        $result = $this->client->resizeVMDisk('ns1000', 105, 'scsi5','25G');
        $this->assertNotEmpty( $result);
    }

    public function testVersion():void
    {
        $result = $this->client->getVersion();
        $this->assertInstanceOf(VersionResponse::class, $result);
    }

    public function testGetClusterStatus():void
    {
        $result = $this->clientCluster->getClusterStatus();
        $this->assertInstanceOf(ClusterResponse::class, $result);
    }


}