<?php
declare(strict_types=1);
namespace Ginernet\Proxmox\VM\Domain\Responses;

final class VmsResponse
{
    private readonly array $vms;


    public function __construct(VmResponse ...$vms){
        $this->vms =$vms;
    }

    public function vms():array{
        return $this->vms;
    }

}