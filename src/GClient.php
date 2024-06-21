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
use Ginernet\Proxmox\Commons\Domain\Models\DiskTypePVE;
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
use Ginernet\Proxmox\VM\App\Service\CreateConfigVMinNode;
use Ginernet\Proxmox\VM\App\Service\CreateVMinNode;
use Ginernet\Proxmox\VM\App\Service\DeleteVMinNode;
use Ginernet\Proxmox\VM\App\Service\GetConfigVMinNode;
use Ginernet\Proxmox\VM\App\Service\ResizeVMDisk;
use Ginernet\Proxmox\VM\App\Service\StartVMinNode;
use Ginernet\Proxmox\VM\App\Service\StopVMinNode;
use Ginernet\Proxmox\VM\Domain\Exceptions\ResizeVMDiskException;
use Ginernet\Proxmox\VM\Domain\Exceptions\VmErrorCreate;
use Ginernet\Proxmox\VM\Domain\Exceptions\VmErrorDestroy;
use Ginernet\Proxmox\VM\Domain\Exceptions\VmErrorStart;
use Ginernet\Proxmox\VM\Domain\Exceptions\VmErrorStop;
use Ginernet\Proxmox\VM\Domain\Model\CpuModel;
use Ginernet\Proxmox\VM\Domain\Model\EfiModel;
use Ginernet\Proxmox\VM\Domain\Model\IpModel;
use Ginernet\Proxmox\VM\Domain\Model\NetModel;
use Ginernet\Proxmox\VM\Domain\Model\Storage\IdeModel;
use Ginernet\Proxmox\VM\Domain\Model\storage\ScsiModel;
use Ginernet\Proxmox\VM\Domain\Model\storage\SataModel;
use Ginernet\Proxmox\VM\Domain\Model\storage\VirtioModel;
use Ginernet\Proxmox\VM\Domain\Model\UserModel;
use Ginernet\Proxmox\VM\Domain\Responses\VmsResponse;

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

     public function createVM(string  $nodeName, int $vmId, ?int $vmCpuCores, ?string $vmName, ?int $vmNetId,
                              ?string $vmNetModel, ?string $vmNetBridge, ?int $vmNetFirewall, ?bool $vmOnBoot, ?string $vmScsiHw,
                              ?string $vmDiskType, ?int    $vmDiskId, ?string $vmDiskStorage, string $vmDiskDiscard, ?string $vmDiskCache, ?string $vmDiskImportFrom, ?string $vmTags,
                              ?int    $vmCloudInitIdeId, ?string $vmCloudInitStorage, ?string $vmBootOrder, ?int $vmAgent,
                              ?int    $vmNetNetId, ?string $vmNetIp, ?string $vmNetGw, ?string $vmOsUserName, ?string $vmOsPassword,
                              ?string $vmCpuType, ?int $vmMemory, ?int $vmMemoryBallon,?string $vmOsType,?string $vmBios,?string $vmMachinePc,
                              ?string $vmEfiStorage, ?int $vmEfiKey):VmsResponse|AuthFailedException|HostUnreachableException|VmErrorCreate
    {
        try {
            $net= new NetModel($vmNetId, $vmNetModel, $vmNetBridge, $vmNetFirewall);
            $scsi= ($vmDiskType == DiskTypePVE::SCSI->value)? new ScsiModel($vmDiskId, $vmDiskStorage, $vmDiskDiscard, $vmDiskCache, $vmDiskImportFrom ):null;
            $ide= ($vmDiskType == DiskTypePVE::IDE->value)? new IdeModel($vmDiskId, $vmDiskStorage, $vmDiskDiscard, $vmDiskCache, $vmDiskImportFrom ):null;
            $sata= ($vmDiskType == DiskTypePVE::SATA->value)? new SataModel($vmDiskId, $vmDiskStorage, $vmDiskDiscard, $vmDiskCache, $vmDiskImportFrom ):null;
            $virtio= ($vmDiskType == DiskTypePVE::VIRTIO->value)? new VirtioModel($vmDiskId, $vmDiskStorage, $vmDiskDiscard, $vmDiskCache, $vmDiskImportFrom ):null;
            $vmNetIp = new IpModel($vmNetNetId,$vmNetIp,$vmNetGw);
            $vm = new CreateVMinNode($this->connection, $this->cookiesPVE);
            $user= new UserModel($vmOsUserName, $vmOsPassword);
            $cpu = new CpuModel($vmCpuType, $vmCpuCores, $vmMemory, $vmMemoryBallon);
            $efi= new EfiModel($vmEfiStorage, $vmEfiKey);
            $result = $vm($nodeName, $vmId, $vmCpuCores, $vmName, $net, $vmOnBoot, $vmScsiHw, $scsi,$ide,$sata,$virtio, $vmTags, $vmBootOrder,$vmAgent, $vmNetIp, $user,$cpu, $vmOsType, $vmBios, $vmMachinePc, $efi);


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
     * @return array|AuthFailedException|null
     */
    public function getConfigVM(string $node, int $vmid)
    {
        try{
            if (!isset($this->cookiesPVE)){
                return new AuthFailedException("Auth failed!!!");
            };
            $getConfigVM =new GetConfigVMinNode($this->connection, $this->cookiesPVE);
            return  $getConfigVM($node, $vmid);
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
    public function getVersion():VersionResponse|AuthFailedException|HostUnreachableException|VersionError{

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

}