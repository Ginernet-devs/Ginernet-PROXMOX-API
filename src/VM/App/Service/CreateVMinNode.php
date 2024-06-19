<?php
declare(strict_types=1);
namespace Ginernet\Proxmox\VM\App\Service;

use Ginernet\Proxmox\Commons\Application\Helpers\GFunctions;
use Ginernet\Proxmox\Commons\Domain\Entities\Connection;
use Ginernet\Proxmox\Commons\Domain\Entities\CookiesPVE;
use Ginernet\Proxmox\Commons\Domain\Exceptions\PostRequestException;
use Ginernet\Proxmox\Commons\infrastructure\GClientBase;
use Ginernet\Proxmox\VM\Domain\Exceptions\VmErrorCreate;
use Ginernet\Proxmox\VM\Domain\Model\CpuModel;
use Ginernet\Proxmox\VM\Domain\Model\EfiModel;
use Ginernet\Proxmox\VM\Domain\Model\Storage\IdeModel;
use Ginernet\Proxmox\VM\Domain\Model\IpModel;
use Ginernet\Proxmox\VM\Domain\Model\NetModel;
use Ginernet\Proxmox\VM\Domain\Model\Storage\SataModel;
use Ginernet\Proxmox\VM\Domain\Model\Storage\ScsiModel;
use Ginernet\Proxmox\VM\Domain\Model\Storage\VirtioModel;
use Ginernet\Proxmox\VM\Domain\Model\UserModel;
use Ginernet\Proxmox\VM\Domain\Responses\VmResponse;
use Ginernet\Proxmox\VM\Domain\Responses\VmsResponse;
use GuzzleHttp\Exception\GuzzleException;

final class CreateVMinNode extends  GClientBase
{
    use GFunctions;


    public function __construct(Connection $connection, CookiesPVE $cookiesPVE)
    {
        parent::__construct($connection, $cookiesPVE);
    }

    public function __invoke(string  $node, int $vmid, ?int $cores, ?string $name, ?NetModel $net, ?bool $onBoot,
                             ?string $scsihw, ?ScsiModel $scsi, ?IdeModel $ide, ?SataModel $sata, ?VirtioModel $virtio, ?string $tags, ?string $boot,
                             ?int    $agent, ?IpModel $ip, ?UserModel $userModel, ?CpuModel $cpuModel, ?string $osType, ?string $bios, ?string $machinePC,
                             ?EfiModel $efi):?VmsResponse
    {




        try {
            $body = [
                'vmid' => $vmid,
                'cores' => $cores,
                'name' => $name,
                'net'.$net->GetIndex() =>$net->toString(),
                'onboot'=> $onBoot,
                'scsihw'=>$scsihw,
                'tags' => $tags,
                'boot'=>'order='.$boot,
                'ipconfig'.$ip->GetIndex() => $ip->toString(),
                'ciuser'=>$userModel->GetUserName(),
                'cipassword'=>$userModel->GetPassword(),
                'cpu' =>$cpuModel->getCpuTypes(),
                'memory'=>$cpuModel->getMemory(),
                'balloon'=>$cpuModel->getBallon(),
                'ostype'=>$osType,
                'bios'=>$bios,
               // 'efi'=>$efi->getStorage().$efi->getKey(),
               // 'machine'=>'type='.$machinePC
            ];
            (isset($scsi))?$body['scsi'.$scsi->GetIndex()]=$scsi->toString():null;
            (isset($ide))?$body['ide'.$ide->GetIndex()]=$ide->toString():null;
            (isset($sata))?$body['sata'.$sata->GetIndex()]=$sata->toString():null;
            (isset($virtio))?$body['virtio'.$virtio->GetIndex()]=$virtio->toString():null;
            $result = $this->Post("nodes/".$node."/qemu/", $body);
            $getContent = json_decode($result->getBody()->getContents());
            return new VmsResponse(...array_map($this->toResponse(), (array)$getContent));
        }catch (PostRequestException $e ){
            if ($e->getCode()===500) throw new VmErrorCreate($e->getMessage());
            var_dump($e->getMessage());
            return throw new VmErrorCreate("Error in create VM");
        }

    }

    public function toResponse():callable
    {
        return static fn($result):VmResponse=>new VmResponse(
            $result[0]
        );
    }
}