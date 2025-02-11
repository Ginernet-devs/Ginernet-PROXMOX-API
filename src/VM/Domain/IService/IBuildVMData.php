<?php
namespace Ginernet\Proxmox\VM\Domain\IService;



interface IBuildVMData {

    public function buildData(): array;

}