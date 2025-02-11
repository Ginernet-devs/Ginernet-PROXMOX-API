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
use Ginernet\Proxmox\VM\Domain\Exceptions\VmErrorStart;
use Ginernet\Proxmox\VM\Domain\Exceptions\VmErrorStop;
use Ginernet\Proxmox\VM\Domain\Exceptions\VncProxyError;
use Ginernet\Proxmox\VM\Domain\Exceptions\VncWebSocketError;
use Ginernet\Proxmox\VM\Domain\Responses\VmsResponse;
use Ginernet\Proxmox\VM\Domain\Responses\VncResponse;
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
use Ginernet\Proxmox\VM\Domain\Exceptions\AgentExecVMException;
use Ginernet\Proxmox\VM\Domain\Exceptions\AgentFileWriteVMException;
use Ginernet\Proxmox\VM\Domain\Exceptions\CapbilitiesMachineException;
use Ginernet\Proxmox\VM\Domain\Exceptions\GetConfigVMException;
use Ginernet\Proxmox\VM\Domain\Exceptions\GetStatusVMException;
use Ginernet\Proxmox\VM\Domain\Exceptions\GetTaskStatusVMException;
use Ginernet\Proxmox\VM\Domain\Exceptions\PingVMDiskException;
use Ginernet\Proxmox\VM\Domain\Exceptions\ShutdownException;
use Ginernet\Proxmox\VM\Domain\Exceptions\VmErrorReset;

class GClientTest extends  TestCase
{

    private LoginResponse $auth;
    private LoginResponse $authCLuster;
    private GClient $client;
    private GClient $clientCluster;

    public function setUp():void{
        $this->client = new GClient($_ENV['HOST'],$_ENV['USERNAME'],$_ENV['PASSWORD'],$_ENV['REALM']);
        $this->auth = $this->client->login();
     //   $this->clientCluster = new GClient($_ENV['HOST_CLUSTER'], $_ENV['USERNAME_CLUSTER'], $_ENV['PASSWORD_CLUSTER'], $_ENV['REALM_CLUSTER']);
      //  $this->authCLuster = $this->clientCluster->login();
    }
    public function testVersion():void
    {
        $result = $this->client->getVersion();
        var_dump($result);die;
        $this->assertInstanceOf(VersionResponse::class, $result);
    }

    //// TESTING LOGIN
    public function testLoginClientOk():void
    {
        $this->assertInstanceOf(LoginResponse::class, $this->auth);
    }

    public function testLoginClientUserNameKO():void
    {

            $client = new GClient($_ENV['HOST_CLUSTER'], 'DDEfed', $_ENV['PASSWORD'], $_ENV['REALM']);
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

    //// TESTING NODES.
    public function testGetNodesOK():void
    {
        $result = $this->client->GetNodes();
        $this->assertInstanceOf(NodesResponse::class, $result);
    }


    //// TESTING STORAGES
    public function testGetStoragesFromNodeOK():void
    {
        $result = $this->client->GetStoragesFromNode("ns1047");
        $this->assertInstanceOf(StoragesResponse::class, $result);
    }


    public function testGetStoragesFromNodeKO():void
    {
        $result = $this->client->GetStoragesFromNode("test");
        $this->assertInstanceOf(StoragesNotFound::class, $result);

    }



    //// TESTING NETWORKS.
    public  function testGetNetworkFromNodeOK():void
    {
        $result = $this->client->GetNetworksFromNode("ns1047");
        $this->assertInstanceOf(NetworksResponse::class, $result);
    }

    public  function testGetNetworkFromNodeKO():void
    {
        $result = $this->client->GetNetworksFromNode("test");
        $this->assertInstanceOf(NetworksNotFound::class, $result);
    }


    //// TESTING CPUS
    public function testGetCpusFromNodeOK():void
    {
        $result = $this->client->GetCpusFromNode("ns1047");
        $this->assertInstanceOf(CpusResponse::class, $result);
    }


    public  function testGetCpusFromNodeKO():void
    {
        try {
            $result = $this->client->GetCpusFromNode("t");
        }catch(\Exception $ex) {
                $this->assertInstanceOf(CpuNotFound::class, $ex);
            }
        }


    //// TESTING VM
    public function testCreateVMOk():void
    {
        $result =$this->client->createVM('ns1047', 102,2,'hostname', 0, 'virtio',
            'vmbr0',1,true, 'virtio-scsi-pci', 'SCSI',0, 'nvme', 'on','directsync','/mnt/pve/nfs-iso/gcp-images/AlmaLinux-8.6_x86_64-minimal.iso',
            'deb12',0, 'nvme','scsi0', 1,0,'5.134.113.50/24','5.134.113.1','root', 'password', 'x86-64-v2-AES', 4096,0,
            'l26' ,'ovmf','pc-q35-8-1', 'nvme',1);
        $this->assertInstanceOf(VmsResponse::class, $result);

    }
    public function testCreateVMError():void
    {
        $result = $this->client->createVM('ns1047', 115,2,'hostname', 0, 'virtio',
            'vmbr0',1,true, 'virtio-scsi-pci', 'SCSI',0, 'nvme', 'on','directsync','/mnt/pve/nfs-iso/gcp-images/Debian-12-x86_64-GridCP-PVE_KVM-20240610.qcow2',
            'deb12',0, 'nvme','scsi0', 1,0,'5.134.113.50/24','5.134.113.1','root', 'password', 'x86-64-v2-AES', 4096,0,
            'l26' ,'ovmf','pc-q35-8-1', 'nvme',1);
        $this->assertInstanceOf(VmErrorCreate::class, $result);
    }


    //// TESTING CONFIGURATION
    public  function testGetVMConfiguration():void
    {
        $result = $this->client->getConfigVM('ns1047',102);
        $this->assertNotEmpty($result);
    }


    //// TEST START VM
    public  function testStartVMOK():void
    {
        $result = $this->client->startVM('ns1047',102);
          $this->assertNotEmpty($result);
    }

    public  function testStartVMErrorKO():void
    {
        $result = $this->client->startVM('nsxxx',102);
        $this->assertInstanceOf(VmErrorStart::class, $result);
    }

    public  function testStartVMVmIdErrorKO():void   {
        $result = $this->client->startVM('ns1047',0);
        $this->assertInstanceOf(VmErrorStart::class, $result);
    }



    //// TEST STOP VM
    public  function testStopVMOK():void
    {
        $result = $this->client->stopVM('ns1047',102);
        $this->assertNotEmpty($result);
    }

    public  function testStopVMErrorKO():void
    {
        try {
            $this->client->stopVM('nsxxx', 108);
        }catch(\Exception $ex){
            $this->assertInstanceOf(VmErrorStop::class, $ex);
        }

    }

    public  function testStopVMVmIdErrorKO():void   {
        try {
            $result = $this->client->stopVM('ns1047', 0);
        }catch(\Exception $ex) {
            $this->assertInstanceOf(VmErrorStop::class, $ex);
        }
    }

    //// TEST RESIZE VM DISK

    public function testResizeVMDiskOk():void
    {
        $result = $this->client->resizeVMDisk('ns1047', 102, 'scsi0','25G');
        $this->assertNotEmpty($result);
    }

    public function testResizeVMDiskKO():void
    {
        $result = $this->client->resizeVMDisk('ns1000', 105, 'scsi5','25G');
        $this->assertNotEmpty( $result);
    }

    //// TEST DELETE VM
    public  function testDeleteVMOK():void
    {
        $result = $this->client->deleteVM('ns1047',102);
        $this->assertNotEmpty($result);
    }

    /// TEST VNC PROXY
    public function testCreateVncproxyOk():void{
        $result =$this->client->createVncProxy("ns1047",101);
        $this->assertInstanceOf(VncResponse::class, $result);
    }

    public function testCreateVncproxyKO():void{
        $result =$this->client->createVncProxy("ns1047",118);
        $this->assertInstanceOf(VncProxyError::class, $result);
    }

    public function testCreateVncWebSocketOk():void{
        $resultProxy =$this->client->createVncProxy("ns1047",101);
        $result = $this->client->createVncWebSocket("ns1047",101, (int) $resultProxy->getPort(),$resultProxy->getTicket() );
        $this->assertNotEmpty( $result);
    }

    public function testCreateVncWebSocketKo():void{
        $resultProxy =$this->client->createVncProxy("ns1047",1010);
        $result = $this->client->createVncWebSocket("ns1047",101, (int) $resultProxy->getPort(),$resultProxy->getTicket() );
        $this->assertInstanceOf(VncWebSocketError::class, $result);
    }

    //// TESTING CLUSTER
   /* public function testVersion():void
    {
        $result = $this->client->getVersion();
        $this->assertInstanceOf(VersionResponse::class, $result);
    }

    public function testGetClusterStatus():void
    {
        $result = $this->clientCluster->getClusterStatus();
        $this->assertInstanceOf(ClusterResponse::class, $result);
    }*/

    public function testcreateConfigVMOk():void{
        $result =$this->client->createConfigVM("ns1047",101, 1, "on","directsync", "/mnt/pve/nfs-iso/gcp-images/Debian-12-x86_64-GridCP-PVE_KVM-20240610.qcow2");
        $this->assertIsString($result);
    }
    public function testcreateConfigVMKo():void{
        $result =$this->client->createConfigVM("ns10xx",101, 1, "on","directsync", "/mnt/pve/nfs-iso/gcp-images/Debian-12-x86_64-GridCP-PVE_KVM-20240610.qcow2");
        $this->assertInstanceOf(GetConfigVMException::class, $result);
    }

    public function testAgentExecVMOk():void{
        $result =$this->client->setAgentExecVM("ns1047",101, ['command' => ['dir']]);
        $this->assertNotEmpty($result);
    }
    public function testAgentExecVMKo():void{
        $result =$this->client->setAgentExecVM("ns1047",101, ['command' => ['dir']]);
        $this->assertInstanceOf(AgentExecVMException::class, $result);
    }


    public function testAgentFileWriteVMOk():void{
        $result =$this->client->agentFileWriteVM("ns1047",101, [
            'content' => '[{000214A0-0000-0000-C000-000000000046}]
            Prop3=19,0
            [InternetShortcut]
            IDList=
            URL=ms-settings:regionlanguage',
            'file' => "C:\Users\Public\Desktop\Language.url"
                    ]);
        $this->assertNotEmpty($result);
    }
    public function testAgentFileWriteVMKo():void{
        $result =$this->client->agentFileWriteVM("ns1047",101, [
            'content' => '[{000214A0-0000-0000-C000-000000000046}]
            Prop3=19,0
            [InternetShortcut]
            IDList=
            URL=ms-settings:regionlanguage',
            'file' => "C:\Users\Public\Desktop\Language.url"
                    ]);
        $this->assertInstanceOf(AgentFileWriteVMException::class, $result);
    }

    public function testPingVMOk():void{
        $result =$this->client->pingVM("ns1047",101);
        $this->assertNotEmpty($result);
    }
    public function testPingVMKo():void{
        $result =$this->client->pingVM("ns10xx",101);
        $this->assertInstanceOf(PingVMDiskException::class, $result);
    }

    public function testgetTaskStatusVMOK():void{
        $result =$this->client->getTaskStatusVM("ns1047",'101');
        $this->assertNotEmpty($result);
    }
    public function testgetTaskStatusVMKO():void{
        $result =$this->client->getTaskStatusVM("ns10xx",'101');
        $this->assertInstanceOf(GetTaskStatusVMException::class, $result);
    }

    public function testSetConfigVMOK(): void
    {
        $params = ['keyboard' => 'es'];
        $result = $this->client->setConfigVM("ns1047", 101, $params);
        $this->assertNotEmpty($result);
    }

    public function testSetConfigVMKO(): void
    {
        $params = [];
        $result = $this->client->setConfigVM("ns10xx", 101, $params);
        $this->assertInstanceOf(GetConfigVMException::class, $result);
    }


    public function testGetStatusVMOK(): void
    {
        $result = $this->client->getStatusVM("ns1047", 101, true);
        $this->assertNotEmpty($result);
    }   

    public function testGetStatusVMKO(): void
    {
        $result = $this->client->getStatusVM("ns10xx", 101, true);
        $this->assertInstanceOf(GetStatusVMException::class, $result);
    }  

    public function testShutdownOK(): void
    {
        $result = $this->client->shutdown("ns1047", 101);
        $this->assertNotEmpty($result);
    }   

    public function testShutdownKO(): void
    {
        $result = $this->client->shutdown("nsxxx", 101);
        $this->assertInstanceOf(ShutdownException::class, $result);
    }   

    public function testResetOK(): void
    {
        $result = $this->client->reset("ns1047", 101);
        $this->assertNotEmpty($result);
    }   

    public function testResetKO(): void
    {
        $result = $this->client->reset("nsxxx", 101);
        $this->assertInstanceOf(VmErrorReset::class, $result);
    }   

    public function testGetCapabilitiesMachineOK(): void
    {
        $result = $this->client->getCapabilitiesMachine("ns1047");
        $this->assertNotEmpty($result);
    }   

    public function testGetCapabilitiesMachineKO(): void
    {
        $result = $this->client->getCapabilitiesMachine("nsxxx");
        $this->assertInstanceOf(CapbilitiesMachineException::class, $result);
    }


}