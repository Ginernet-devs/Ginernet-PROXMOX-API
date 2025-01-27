<?php
declare(strict_types=1);
namespace Ginernet\Proxmox;

use Ginernet\Proxmox\Auth\App\Service\Login;
use Ginernet\Proxmox\Auth\Domain\Responses\LoginResponse;
use Ginernet\Proxmox\Cluster\App\GetClusterStatus;
use Ginernet\Proxmox\Cluster\Domain\Exceptions\ClusterNotFound;
use Ginernet\Proxmox\Cluster\Domain\Responses\ClusterResponse;
use Ginernet\Proxmox\Commons\Domain\Entities\Connection;
use Ginernet\Proxmox\Commons\Domain\Entities\CookiesPVE;
use Ginernet\Proxmox\Commons\Domain\Exceptions\AuthFailedException;
use Ginernet\Proxmox\Commons\Domain\Exceptions\HostUnreachableException;
use Ginernet\Proxmox\Cpus\App\Service\GetCpuFromNode;
use Ginernet\Proxmox\Cpus\Domain\Exceptions\CpuNotFound;
use Ginernet\Proxmox\Cpus\Domain\Reponses\CpusResponse;
use Ginernet\Proxmox\Networks\App\Service\GetNetworksFromNode;
use Ginernet\Proxmox\Networks\Domain\Exceptions\NetworksNotFound;
use Ginernet\Proxmox\Networks\Domain\Responses\NetworksResponse;
use Ginernet\Proxmox\Nodes\App\Service\GetNodes;
use Ginernet\Proxmox\Nodes\Domain\Responses\NodesResponse;
use Ginernet\Proxmox\Proxmox\Version\App\Service\GetVersionFromNode;
use Ginernet\Proxmox\Proxmox\Version\Domain\Exceptions\VersionError;
use Ginernet\Proxmox\Proxmox\Version\Domain\Responses\VersionResponse;
use Ginernet\Proxmox\Storages\App\Service\GetStoragesFromNode;
use Ginernet\Proxmox\Storages\Domain\Exceptions\StoragesNotFound;
use Ginernet\Proxmox\Storages\Domain\Responses\StoragesResponse;
use Ginernet\Proxmox\VM\App\Service\AgentExecStatusVMinNode;
use Ginernet\Proxmox\VM\App\Service\CapbilitiesMachineVMinNode;
use Ginernet\Proxmox\VM\App\Service\CreateConfigVMinNode;
use Ginernet\Proxmox\VM\App\Service\CreateVm;
use Ginernet\Proxmox\VM\App\Service\CreateVncProxy;
use Ginernet\Proxmox\VM\App\Service\CreateVncWebSocket;

use Ginernet\Proxmox\VM\App\Service\DeleteVMinNode;
use Ginernet\Proxmox\VM\App\Service\GetStatusVMinNode;
use Ginernet\Proxmox\VM\App\Service\GetTaskStatusVmNode;
use Ginernet\Proxmox\VM\App\Service\PingVMinNode;
use Ginernet\Proxmox\VM\App\Service\ResetVMNode;
use Ginernet\Proxmox\VM\App\Service\ResizeVMDisk;
use Ginernet\Proxmox\VM\App\Service\SetAgentExecVMinNode;
use Ginernet\Proxmox\VM\App\Service\SetAgentFileWriteVMinNode;
use Ginernet\Proxmox\VM\App\Service\SetConfigVMinNode;
use Ginernet\Proxmox\VM\App\Service\ShutdownVMNode;
use Ginernet\Proxmox\VM\App\Service\StartVMinNode;
use Ginernet\Proxmox\VM\App\Service\StopVMinNode;
use Ginernet\Proxmox\VM\Domain\Exceptions\CapbilitiesMachineException;
use Ginernet\Proxmox\VM\Domain\Exceptions\ResizeVMDiskException;
use Ginernet\Proxmox\VM\Domain\Exceptions\VmErrorCreate;
use Ginernet\Proxmox\VM\Domain\Exceptions\VmErrorDestroy;
use Ginernet\Proxmox\VM\Domain\Exceptions\VmErrorReset;
use Ginernet\Proxmox\VM\Domain\Exceptions\VmErrorStart;
use Ginernet\Proxmox\VM\Domain\Exceptions\VmErrorStop;
use Ginernet\Proxmox\VM\Domain\Exceptions\VncProxyError;
use Ginernet\Proxmox\VM\Domain\Exceptions\VncWebSocketError;
use Ginernet\Proxmox\VM\Domain\Responses\VmsResponse;
use Ginernet\Proxmox\VM\Domain\Responses\VncResponse;

/**
 *
 */
class GClient
{
    /**
     * @var Connection
     */
    private Connection $connection;
    /**
     * @var string
     */
    private string $CSRFPreventionToken;
    /**
     * @var CookiesPVE
     */
    private CookiesPVE $cookiesPVE;

    /**
     * @param $hostname
     * @param $username
     * @param $password
     * @param $realm
     * @param $port
     */
    public function __construct($hostname, $username, $password, $realm, $port = 8006)
    {
        $this->connection =  new Connection($hostname, $port, $username, $password, $realm);
    }


    /**
     * @return LoginResponse|AuthFailedException|HostUnreachableException
     */
    public function login(): LoginResponse|AuthFailedException|HostUnreachableException
    {
        try {

            $auth = new Login($this->connection, null);
            $result = $auth();
            if (is_null($result->getCookies()))  return new AuthFailedException();
            $this->cookiesPVE = new CookiesPVE($result->getCSRFPreventionToken(), $result->getCookies(), $result->getTicket());
            return $result;
        } catch (AuthFailedException $ex) {
            return new AuthFailedException();
        } catch (HostUnreachableException $ex) {
            return new HostUnreachableException();
        }
    }

    /**
     * @return NodesResponse|AuthFailedException|HostUnreachableException
     */
    public function GetNodes(): NodesResponse|AuthFailedException|HostUnreachableException
    {
        try {
            if (!isset($this->cookiesPVE)){
                return new AuthFailedException("Auth failed!!!");
            };
            $nodes = new GetNodes($this->connection, $this->cookiesPVE);
            return $nodes();
        }catch (AuthFailedException $ex) {
            return new AuthFailedException();
        } catch (HostUnreachableException $ex) {
            return new HostUnreachableException();
        }
    }

    /**
     * @param string $node
     * @return StoragesResponse|AuthFailedException|HostUnreachableException|StoragesNotFound
     */
    public function GetStoragesFromNode(string $node):StoragesResponse |AuthFailedException|HostUnreachableException|StoragesNotFound
    {
        try {
            if (!isset($this->cookiesPVE)){
                return new AuthFailedException("Auth failed!!!");
            };
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

    /**
     * @param string $node
     * @return NetworksResponse|AuthFailedException|HostUnreachableException|NetworksNotFound
     */
    public function GetNetworksFromNode(string $node):NetworksResponse|AuthFailedException|HostUnreachableException|NetworksNotFound
    {
        try {
            if (!isset($this->cookiesPVE)){
                return new AuthFailedException("Auth failed!!!");
            };
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

    /**
     * @param string $node
     * @return CpusResponse|AuthFailedException|HostUnreachableException|CpuNotFound
     */
    public function GetCpusFromNode(string $node):CpusResponse|AuthFailedException|HostUnreachableException|CpuNotFound
    {
        try {
            if (!isset($this->cookiesPVE)){
                return new AuthFailedException("Auth failed!!!");
            };
            $networks = new GetCpuFromNode($this->connection, $this->cookiesPVE);
            return $networks($node);
        }catch (AuthFailedException $ex){
            return new AuthFailedException();
        }catch(HostUnreachableException $ex){
            return new HostUnreachableException();
        }catch(CpuNotFound $ex){
            return  new CpuNotFound();
        }

    }

    /**
     * @param string $nodeName
     * @param int $vmId
     * @param int|null $vmCpuCores
     * @param string|null $vmName
     * @param int|null $vmNetId
     * @param string|null $vmNetModel
     * @param string|null $vmNetBridge
     * @param int|null $vmNetFirewall
     * @param bool|null $vmOnBoot
     * @param string|null $vmScsiHw
     * @param string|null $vmDiskType
     * @param int|null $vmDiskId
     * @param string|null $vmDiskStorage
     * @param string $vmDiskDiscard
     * @param string|null $vmDiskCache
     * @param string|null $vmDiskImportFrom
     * @param string|null $vmTags
     * @param int|null $vmCloudInitIdeId
     * @param string|null $vmCloudInitStorage
     * @param string|null $vmBootOrder
     * @param int|null $vmAgent
     * @param int|null $vmNetNetId
     * @param string|null $vmNetIp
     * @param string|null $vmNetGw
     * @param string|null $vmOsUserName
     * @param string|null $vmOsPassword
     * @param string|null $vmCpuType
     * @param int|null $vmMemory
     * @param int|null $vmMemoryBallon
     * @return VmsResponse|AuthFailedException|HostUnreachableException|VmErrorCreate
     */



     public function createVM(
                              string  $nodeName, int $vmId, ?int $vmCpuCores, ?string $vmName, ?int $vmNetId,
                              ?string $vmNetModel, ?string $vmNetBridge, ?int $vmNetFirewall, ?bool $vmOnBoot,
                              ?string $vmScsiHw, ?string $vmDiskType, ?int    $vmDiskId, ?string $vmDiskStorage,
                              ?string $vmDiskDiscard, ?string $vmDiskCache, ?string $vmDiskImportFrom, ?string $vmTags,
                              ?int    $vmCloudInitIdeId = null, ?string $vmCloudInitStorage, ?string $vmBootOrder, ?int $vmAgent,
                              ?int    $vmNetNetId, ?string $vmNetIp, ?string $vmNetGw, ?string $vmOsUserName,
                              ?string $vmOsPassword, ?string $vmCpuType, ?int $vmMemory = null, ?int $vmMemoryBallon = null,
                              ?string $vmOsType = null,?string $vmBios = null,?string $vmMachinePc = null,
                              ?string $vmEfiStorage = null, ?int $vmEfiKey = null,
                              ?string $efidisckNvme = null, ?string $efidisckEnrroled = null,
                              ?string $tpmstateNvme = null, ?string $tpmstateVersion = null,
                              ?string $soBuild = 'Deb12'
                            )
    {
        try {

            $vm = new CreateVm($this->connection, $this->cookiesPVE);

            $result = $vm(
                            $nodeName, $vmId, $vmCpuCores, $vmName, $vmNetId, $vmNetModel, $vmNetBridge, $vmNetFirewall,
                            $vmOnBoot, $vmScsiHw, $vmDiskType, $vmDiskId, $vmDiskStorage, $vmDiskDiscard, $vmDiskCache, 
                            $vmDiskImportFrom, $vmTags, $vmCloudInitIdeId, $vmCloudInitStorage, $vmBootOrder, $vmAgent, 
                            $vmNetNetId, $vmNetIp, $vmNetGw, $vmOsUserName, $vmOsPassword, $vmCpuType, $vmMemory, 
                            $vmMemoryBallon, $vmOsType, $vmBios, $vmMachinePc, $vmEfiStorage, $vmEfiKey, $efidisckNvme, 
                            $efidisckEnrroled, $tpmstateNvme, $tpmstateVersion, $soBuild
                         );
           
            return $result;
        }catch (AuthFailedException $ex){
            return new AuthFailedException($ex);
        }catch(HostUnreachableException $ex) {
            return new HostUnreachableException($ex);
        }catch (VmErrorCreate $ex){
            return new VmErrorCreate($ex->getMessage());
        }
    }

    /**
     * @param string $node
     * @param int $vmid
     * @param int|null $index
     * @param string|null $discard
     * @param string|null $cache
     * @param string|null $import
     * @return string|AuthFailedException|HostUnreachableException|ResizeVMDiskException
     */
     public function createConfigVM(string $node, int $vmid, ?int $index, ?string $discard, ?string $cache, ?string $import): string|AuthFailedException|HostUnreachableException|ResizeVMDiskException
    {
        try{
            if (!isset($this->cookiesPVE)){
                return new AuthFailedException("Auth failed!!!");
            };
            $configVM = new CreateConfigVMinNode($this->connection, $this->cookiesPVE);
            return $configVM($node,$vmid,0, $discard, $cache,$import);
        }catch (AuthFailedException $ex){
            return new AuthFailedException($ex);
        }catch(HostUnreachableException $ex) {
            return new HostUnreachableException($ex);
        }catch (ResizeVMDiskException $ex){
            return new ResizeVMDiskException($ex->getMessage());
        }
    }

    /**
     * @param string $node
     * @param int $vmid
     * @param array $command
     * @return array|AuthFailedException|null
     */
    public function setAgentExecVM(string $node, int $vmid, array $command = [])
    {
        try{

            if (!isset($this->cookiesPVE)){
                return new AuthFailedException("Auth failed!!!");
            };

            $getConfigVM =new SetAgentExecVMinNode($this->connection, $this->cookiesPVE);
            return  $getConfigVM($node, $vmid, $command);

        }catch(AuthFailedException $ex)
        {
            return new AuthFailedException($ex);
        }

    }

    /**
     * @param string $node
     * @param int $vmid
     * @param array $command
     * @return array|AuthFailedException|null
     */
    public function agentFileWriteVM(string $node, int $vmid, array $command = [])
    {
        try{

            if (!isset($this->cookiesPVE)){
                return new AuthFailedException("Auth failed!!!");
            };

            $getConfigVM =new SetAgentFileWriteVMinNode($this->connection, $this->cookiesPVE);
            return  $getConfigVM($node, $vmid, $command);
        }catch(AuthFailedException $ex)
        {
            return new AuthFailedException($ex);
        }
    }

    /**
     * @param string $node
     * @param int $vmid
     * @param array $command
     * @return array|AuthFailedException|null
     */

    public function agentExecStatusVM(string $node, int $vmid, string $pid)
    {
        try{
            if (!isset($this->cookiesPVE)){
                return new AuthFailedException("Auth failed!!!");
            };
            $status =new AgentExecStatusVMinNode($this->connection, $this->cookiesPVE);
            return  $status($node, $vmid, $pid);
        }catch(AuthFailedException $ex)
        {
            return new AuthFailedException($ex);
        }
    }

    /**
     * @param string $node
     * @param int $vmid
     * @return array|AuthFailedException|null
     */
    public function pingVM(string $node, int $vmid)
    {
        try{
            if (!isset($this->cookiesPVE)){
                return new AuthFailedException("Auth failed!!!");
            };

            $pingVM =new PingVMinNode($this->connection, $this->cookiesPVE);

            return  $pingVM($node, $vmid);

        }catch(AuthFailedException $ex)
        {
            return new AuthFailedException($ex);
        }

    }

    /**
     * @param string $upid
     * @return array|AuthFailedException|null
     */

    public function getTaskStatusVM(string $node, string $upid)
    {
        try{
            if (!isset($this->cookiesPVE)){
                return new AuthFailedException("Auth failed!!!");
            };

            $taskStatusVM =new GetTaskStatusVmNode($this->connection, $this->cookiesPVE);
            return  $taskStatusVM($node, $upid);
        }catch(AuthFailedException $ex)
        {
            return new AuthFailedException($ex);
        }
    }



    /**
     * @param string $node
     * @param int $vmid
     * @return array|AuthFailedException|null
     */
    public function setConfigVM(string $node, int $vmid, array $params = [])
    {
        try{
            if (!isset($this->cookiesPVE)){
                return new AuthFailedException("Auth failed!!!");
            };
            $getConfigVM =new SetConfigVMinNode($this->connection, $this->cookiesPVE);
            return  $getConfigVM($node, $vmid, $params);
        }catch(AuthFailedException $ex)
        {
            return new AuthFailedException($ex);
        }
    }

    /**
     * @param string $node
     * @param int $vmid
     * @return array|AuthFailedException|null
     */
    public function getStatusVM(string $node, int $vmid, bool $current = false)
    {
        try{
            if (!isset($this->cookiesPVE)){
                return new AuthFailedException("Auth failed!!!");
            };

            $getStatusVM =new GetStatusVMinNode($this->connection, $this->cookiesPVE);
            return  $getStatusVM($node, $vmid, $current);
        }catch(AuthFailedException $ex)
        {
            return new AuthFailedException($ex);
        }
    }

    /**
     * @param string $node
     * @param int $vmid
     * @return string|AuthFailedException|HostUnreachableException|VmErrorStart
     */
     public function startVM(string $node, int $vmId):string|AuthFailedException|HostUnreachableException|VmErrorStart
    {
        try{
            if (!isset($this->cookiesPVE)){
                return new AuthFailedException("Auth failed!!!");
            };
            $getConfigVM =new StartVMinNode($this->connection, $this->cookiesPVE);
            return  $getConfigVM($node, $vmId);
        }catch(AuthFailedException $ex)
        {
            return new AuthFailedException($ex);
        }catch(HostUnreachableException $ex) {
            return new HostUnreachableException($ex);
        }catch (VmErrorStart $ex){
            return new VmErrorStart($ex->getMessage());
        }
    }

    /**
     * @param string $node
     * @param int $vmId
     * @return string|AuthFailedException|HostUnreachableException|VmErrorStop
     * @throws VmErrorStop
     */
    public function stopVM(string $node, int $vmId):string|AuthFailedException|HostUnreachableException|VmErrorStop
    {
        try{
            if (!isset($this->cookiesPVE)){
                return new AuthFailedException("Auth failed!!!");
            };
            $getConfigVM =new StopVMinNode($this->connection, $this->cookiesPVE);
            return  $getConfigVM($node, $vmId);
        }catch(AuthFailedException $ex)
        {
            return new AuthFailedException($ex);
        }catch(HostUnreachableException $ex) {
            return new HostUnreachableException($ex);
        }catch (VmErrorStart $ex){
            return new VmErrorStop($ex->getMessage());
        }
    }

    /**
     * @param string $node
     * @param int $vmId
     * @return string|AuthFailedException|HostUnreachableException|VmErrorDestroy
     */
    public function deleteVM(string $node, int $vmId):string|AuthFailedException|HostUnreachableException|VmErrorDestroy
    {
        try{
            if (!isset($this->cookiesPVE)){
                return new AuthFailedException("Auth failed!!!");
            };
            $deleteVMinNode =new DeleteVMinNode($this->connection, $this->cookiesPVE);
            return  $deleteVMinNode($node, $vmId);
        }catch(AuthFailedException $ex)
        {
            return new AuthFailedException($ex);
        }catch(HostUnreachableException $ex) {
            return new HostUnreachableException($ex);
        } catch (VmErrorDestroy $ex) {
            return new VmErrorDestroy($ex->getMessage());
        }
    }

    /**
     * @param string $node
     * @param int $vmId
     * @return string|AuthFailedException|HostUnreachableException|VmErrorDestroy
     */
    public function shutdown(string $node, int $vmId)
    {
        try{
            if (!isset($this->cookiesPVE)){
                return new AuthFailedException("Auth failed!!!");
            };
            $shutdown =new ShutdownVMNode($this->connection, $this->cookiesPVE);

            return  $shutdown($node, $vmId);

        }catch(AuthFailedException $ex)
        {
            return new AuthFailedException($ex);
        }catch(HostUnreachableException $ex) {
            return new HostUnreachableException($ex);
        } catch (VmErrorDestroy $ex) {
            return new VmErrorDestroy($ex->getMessage());
        }
    }

    /**
     * @param string $node
     * @param int $vmId
     * @return string|AuthFailedException|HostUnreachableException|VmErrorReset
     */
    public function reset(string $node, int $vmId)
    {

        try{
            if (!isset($this->cookiesPVE)){
                return new AuthFailedException("Auth failed!!!");
            };

            $reset =new ResetVMNode($this->connection, $this->cookiesPVE);
            return  $reset($node, $vmId);
        }catch(AuthFailedException $ex)
        {
            return new AuthFailedException($ex);
        }catch(HostUnreachableException $ex) {
            return new HostUnreachableException($ex);
        } catch (VmErrorReset $ex) {
            return new VmErrorReset($ex->getMessage());
        }
    }


    /**
     * @param string $node
     * @param int $vmid
     * @param string|null $disk
     * @param string|null $size
     * @return string|AuthFailedException|HostUnreachableException|ResizeVMDiskException
     */
    public function resizeVMDisk(string $node, int $vmid, ?string $disk, ?string $size): string|AuthFailedException|HostUnreachableException|ResizeVMDiskException
    {
        try{
            if (!isset($this->cookiesPVE)){
                return new AuthFailedException("Auth failed!!!");
            };
            $resizeVMDisk = new ResizeVMDisk($this->connection, $this->cookiesPVE);
            return $resizeVMDisk($node, $vmid,$disk,$size);
        }catch (AuthFailedException $ex){
            return new AuthFailedException($ex);
        }catch(HostUnreachableException $ex) {
            return new HostUnreachableException($ex);
        }catch (ResizeVMDiskException $ex){
            return new ResizeVMDiskException($ex->getMessage());
        }
    }

    /**
     * @return VersionResponse|AuthFailedException|HostUnreachableException|VersionError
     */
    public function getVersion():VersionResponse|AuthFailedException|HostUnreachableException|VersionError
    {

        try{
            if (!isset($this->cookiesPVE)){
                return new AuthFailedException("Auth failed!!!");
            };
            $version = new GetVersionFromNode($this->connection,$this->cookiesPVE);
            return  $version();
        }catch(AuthFailedException $ex){
            return new AuthFailedException($ex);
        }catch(HostUnreachableException $ex){
            return new HostUnreachableException($ex);
        }catch(VersionError $ex){
            return new VersionError($ex->getMessage());

        }
    }
    



    /**
     * @return mixed|AuthFailedException|HostUnreachableException|CapbilitiesMachineException
     */
    public function getCapabilitiesMachine(string $node)
    {

        try{
            if (!isset($this->cookiesPVE)){
                return new AuthFailedException("Auth failed!!!");
            };

            $typeMachine = new CapbilitiesMachineVMinNode($this->connection,$this->cookiesPVE);
            return  $typeMachine($node);

        }catch(AuthFailedException $ex){
            return new AuthFailedException($ex);
        }catch(HostUnreachableException $ex){
            return new HostUnreachableException($ex);
        }catch(CapbilitiesMachineException $ex){
            return new CapbilitiesMachineException($ex->getMessage());
        }
    }


    /**
     * @return ClusterResponse|AuthFailedException|HostUnreachableException|ClusterNotFound
     */
    public function getClusterStatus():ClusterResponse|AuthFailedException|HostUnreachableException|ClusterNotFound
    {
        try {
            if (!isset($this->cookiesPVE)){
                return new AuthFailedException("Auth failed!!!");
            };
            $status = new GetClusterStatus($this->connection, $this->cookiesPVE);
            return $status();

        }catch (AuthFailedException $ex){
            return new AuthFailedException($ex);
        } catch (HostUnreachableException $ex) {
            return new HostUnreachableException($ex);
        }catch (ClusterNotFound $ex)
        {
            return new ClusterNotFound();
        }
    }

    /**
     * @param string $node
     * @param int $vmid
     * @return VncResponse|AuthFailedException|VncProxyError
     */
    public function createVncProxy(string $node, int $vmid):VncResponse|AuthFailedException|VncProxyError
    {
        try{
            if(!isset($this->cookiesPVE)){
                return new AuthFailedException('Auth failed !!!');
            };
            $vncProxy = new CreateVncProxy($this->connection, $this->cookiesPVE);
             return $vncProxy($node, $vmid);
        }catch(VncProxyError $ex){
            return new VncProxyError($ex->getMessage());
        }
    }


    public function createVncWebSocket(string $node, int $vmid, int $port, string $vncticket)
    {
        try {
            if (!isset($this->cookiesPVE)) {
                return new AuthFailedException('Auth failed !!!');
            };
            $vncWebSocket = new CreateVncWebSocket($this->connection, $this->cookiesPVE);
            $result = $vncWebSocket($node, $vmid, $port, $vncticket);
            return $result;

        }catch(VncWebSocketError $ex){
            return new VncWebSocketError($ex->getMessage());
        }
    }
}