<?php

declare(strict_types=1);

namespace Ginernet\Proxmox\VM\Domain\Model;



use Ginernet\Proxmox\VM\Domain\Model\Storage\StorageInterface;



final class  MachineModel

{



    private string $text;

    private ?string $type = null;
    private ?string $viommu= null;



    public function __construct( ?string $type = null, ?string $viommu = null)

    {
        
        $this->type = $type;

        $this->viommu = $viommu;

        $this->text='';

    }



    public function GetType(): ?string

    {

        return $this->type;

    }



    public function GetViommu(): ?string

    {

        return $this->viommu;

    }



    public function toString():?string{

        //                       --efidisk0 nvme:0,pre-enrolled-keys=1
        if($this->GetType()) $this->text .= '' . $this->GetType();

        if($this->GetViommu()){ 

            if($this->text != '') $this->text .= ',';
            $this->text .="viommu=".$this->GetViommu();
        }
        return $this->text;

    }



}