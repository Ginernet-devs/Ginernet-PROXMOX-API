<?php
declare(strict_types=1);
namespace Ginernet\Proxmox;

use Ginernet\Proxmox\Auth\App\Service\Login;
use Ginernet\Proxmox\Auth\Domain\Responses\LoginResponse;
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
use Ginernet\Proxmox\Nodes\App\Service\GetNode;
use Ginernet\Proxmox\Nodes\App\Service\GetNodes;
use Ginernet\Proxmox\Nodes\Domain\Responses\NodesResponse;
use Ginernet\Proxmox\Proxmox\Version\App\Service\GetVersionFromNode;
use Ginernet\Proxmox\Proxmox\Version\Domain\Exceptions\VersionError;
use Ginernet\Proxmox\Proxmox\Version\Domain\Responses\VersionResponse;
use Ginernet\Proxmox\Storages\App\Service\GetStoragesFromNode;
use Ginernet\Proxmox\Storages\Domain\Exceptions\StoragesNotFound;
use Ginernet\Proxmox\Storages\Domain\Responses\StoragesResponse;
use Ginernet\Proxmox\VM\App\Service\ConfigVMinNode;
use Ginernet\Proxmox\VM\App\Service\CreateVMinNode;
use Ginernet\Proxmox\VM\App\Service\ResizeVMDisk;
use Ginernet\Proxmox\VM\Domain\Exceptions\ResizeVMDiskException;
use Ginernet\Proxmox\VM\Domain\Exceptions\VmErrorCreate;
use Ginernet\Proxmox\VM\Domain\Model\CpuModel;
use Ginernet\Proxmox\VM\Domain\Model\IdeModel;
use Ginernet\Proxmox\VM\Domain\Model\IpModel;
use Ginernet\Proxmox\VM\Domain\Model\NetModel;
use Ginernet\Proxmox\VM\Domain\Model\ScsiModel;
use Ginernet\Proxmox\VM\Domain\Model\UserModel;
use Ginernet\Proxmox\VM\Domain\Responses\VmsResponse;

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
            if (is_null($result))  return new AuthFailedException();
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
    public function GetCpusFromNode(string $node):CpusResponse|AuthFailedException|HostUnreachableException|CpuNotFound
    {
        try {
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

    public function createVM(string $node, int $vmid, ?int $cores, ?string $name, ?int $netId,
                             ?string $netModel, ?string $netBridge, ?int $netFirewall, ?bool $OnBoot, ?string $scsihw,
                            ?int $scsiId, int $main, string $discard, ?string $cache, ?string $importFrom,?string $tags,
                             ?int $ideId,?string $ideFile, ?string $boot, ?string $bootDisk,?string $agent,
                             ?int $ipIndex, ?string $ip, ?string $gateway, ?string $userName, ?string $password,
                            ?string $cpuTypes, ?int $memory, ?int $ballon  ):VmsResponse|AuthFailedException|HostUnreachableException|VmErrorCreate
    {
        try {
            $net= new NetModel($netId, $netModel, $netBridge, $netFirewall);
            $scsi = new ScsiModel($scsiId, $main, $discard, $cache, $importFrom );
            $ide = new IdeModel($ideId,$ideFile);
            $ip = new IpModel($ipIndex,$ip,$gateway);
            $vm = new CreateVMinNode($this->connection, $this->cookiesPVE);
            $user= new UserModel($userName, $password);
            $cpu = new CpuModel($cpuTypes, $cores, $memory, $ballon);
            return $vm($node, $vmid, $cores, $name, $net, $OnBoot, $scsihw, $scsi, $tags,$ide, $boot, $bootDisk, $agent, $ip, $user, $cpu);
        }catch (AuthFailedException $ex){
            return new AuthFailedException($ex);
        }catch(HostUnreachableException $ex) {
            return new HostUnreachableException($ex);
        }catch (VmErrorCreate $ex){
            return new VmErrorCreate($ex->getMessage());
        }
    }

    public function configVM(string $node, int $vmid, ?int $index,?string $discard, ?string $cache, ?string $import): string|AuthFailedException|HostUnreachableException|ResizeVMDiskException
    {
        try{
            $configVM = new ConfigVMinNode($this->connection, $this->cookiesPVE);
            return $configVM($node,$vmid,0, $discard, $cache,$import);
        }catch (AuthFailedException $ex){
            return new AuthFailedException($ex);
        }catch(HostUnreachableException $ex) {
            return new HostUnreachableException($ex);
        }catch (ResizeVMDiskException $ex){
            return new ResizeVMDiskException($ex->getMessage());
        }
    }
    public function resizeVMDisk(string $node, int $vmid, ?string $disk, ?string $size): string|AuthFailedException|HostUnreachableException|ResizeVMDiskException
    {
        try{
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

}