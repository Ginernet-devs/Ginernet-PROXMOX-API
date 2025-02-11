<?php
declare(strict_types=1);

namespace Ginernet\Proxmox\VM\App\Service\Help\So\Linux;

use Ginernet\Proxmox\Commons\Domain\Models\DiskTypePVE;
use Ginernet\Proxmox\VM\Domain\IService\IBuildVMData;
use Ginernet\Proxmox\VM\Domain\Model\CpuModel;
use Ginernet\Proxmox\VM\Domain\Model\EfidisckModel;
use Ginernet\Proxmox\VM\Domain\Model\EfiModel;

use Ginernet\Proxmox\VM\Domain\Model\Storage\IdeModel;

use Ginernet\Proxmox\VM\Domain\Model\IpModel;
use Ginernet\Proxmox\VM\Domain\Model\MachineModel;
use Ginernet\Proxmox\VM\Domain\Model\NetModel;

use Ginernet\Proxmox\VM\Domain\Model\Storage\SataModel;

use Ginernet\Proxmox\VM\Domain\Model\Storage\ScsiModel;

use Ginernet\Proxmox\VM\Domain\Model\Storage\VirtioModel;
use Ginernet\Proxmox\VM\Domain\Model\TpmstateModel;
use Ginernet\Proxmox\VM\Domain\Model\UserModel;




final class CreateDataForLinuxVM implements IBuildVMData
{



    public function __construct(
                                    private string  $nodeName, private int $vmId, private ?int $vmCpuCores, private ?string $vmName, private ?int $vmNetId,  // NOSONAR
                                    private ?string $vmNetModel, private ?string $vmNetBridge, private ?int $vmNetFirewall, private ?bool $vmOnBoot, // NOSONAR
                                    private ?string $vmScsiHw, private ?string $vmDiskType, private ?int    $vmDiskId, private ?string $vmDiskStorage, // NOSONAR
                                    private ?string $vmDiskDiscard, private ?string $vmDiskCache, private ?string $vmDiskImportFrom, private ?string $vmTags, // NOSONAR
                                    private ?int    $vmCloudInitIdeId, private ?string $vmCloudInitStorage, private ?string $vmBootOrder, private ?int $vmAgent, // NOSONAR
                                    private ?int    $vmNetNetId, private ?string $vmNetIp, private ?string $vmNetGw, private ?string $vmOsUserName, // NOSONAR
                                    private ?string $vmOsPassword, private ?string $vmCpuType, private ?int $vmMemory = null, private ?int $vmMemoryBallon = null, // NOSONAR
                                    private ?string $vmOsType = null, private ?string $vmBios = null,private ?string $vmMachinePc = null, // NOSONAR
                                    private ?string $vmEfiStorage = null, private ?int $vmEfiKey = null, // NOSONAR
                                    private ?string $efidisckNvme = null, private ?string $efidisckEnrroled = null, // NOSONAR
                                    private ?string $tpmstateNvme = null, private ?string $tpmstateVersion = null // NOSONAR
                                ) // NOSONAR
    {

    }
    
    
    public function buildData(): array
    {
        
        $net= new NetModel($this->vmNetId, $this->vmNetModel, $this->vmNetBridge, $this->vmNetFirewall);
            
        $scsi= null;
        if (strtolower($this->vmDiskType) == strtolower(DiskTypePVE::SCSI)) {
            $scsi = new ScsiModel($this->vmDiskId, $this->vmDiskStorage, $this->vmDiskDiscard, $this->vmDiskCache, $this->vmDiskImportFrom );
        }
        
        $ide= new IdeModel($this->vmDiskId, $this->vmDiskStorage, $this->vmDiskDiscard, $this->vmDiskCache, $this->vmDiskImportFrom );
 
        
        $sata=new SataModel($this->vmDiskId, $this->vmDiskStorage, $this->vmDiskDiscard, $this->vmDiskCache, $this->vmDiskImportFrom ); // NOSONAR

        $virtio= new VirtioModel($this->vmDiskId, $this->vmDiskStorage, $this->vmDiskDiscard, $this->vmDiskCache, $this->vmDiskImportFrom ); // NOSONAR

        $ip = new IpModel($this->vmNetNetId,$this->vmNetIp,$this->vmNetGw);


        


        $user= new UserModel($this->vmOsUserName, $this->vmOsPassword);

        $cpu = new CpuModel($this->vmCpuType, $this->vmCpuCores, $this->vmMemory, $this->vmMemoryBallon);

        $efi= !is_null($this->vmEfiKey)? new EfiModel($this->vmEfiStorage, $this->vmEfiKey) : null; // NOSONAR

        $machinePc= !is_null($this->vmMachinePc)? new MachineModel($this->vmMachinePc, $this->vmNetModel) : null; // NOSONAR

        $tpmstate= !is_null($this->tpmstateVersion)? new TpmstateModel(0, $this->tpmstateNvme, null, null, null, $this->tpmstateVersion) : null; // NOSONAR

        $efiDisck= !is_null($this->efidisckNvme)? new EfidisckModel(0, null, $this->efidisckNvme, null, null, $this->efidisckEnrroled) : null; // NOSONAR



        $body = [
            'vmid' => $this->vmId,
            'cores' => $this->vmCpuCores,
            'name' => $this->vmName,
            'onboot'=> $this->vmOnBoot,
            'agent' => 'enabled='.$this->vmAgent,
            'scsihw'=>$this->vmScsiHw,
            'net'.$net->GetIndex() =>$net->toString(),
            'tags' => $this->vmTags,
            'boot'=>'order='.$this->vmBootOrder,
            'cpu' =>$cpu->getCpuTypes(),
            'memory'=>$cpu->getMemory(),
            'balloon'=>$cpu->getBallon(),
            'ide0' => 'none,media=cdrom',

        ];
        
        

            
        
        if (isset($user)) {
            $body['ciuser'] = $user->GetUserName();
            $body['cipassword'] = $user->GetPassword();
        }
        
        if (!is_null($ip->toString())) {
            $body[ 'ipconfig'.$ip->GetIndex() ] = $ip->toString();
        }
        
        (!is_null($this->vmCloudInitIdeId) && $this->vmCloudInitIdeId != 0)?$body['ide2']=$ide->GetDiskStorage() .':cloudinit':null;



        (isset($scsi))?$body['scsi'.$scsi->GetIndex()]=$scsi->toString():null;
        (isset($this->vmOsType))?$body['ostype']=$this->vmOsType:null;

        return $body;
    }

}