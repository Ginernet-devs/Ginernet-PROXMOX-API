<?php
declare(strict_types=1);
namespace Ginernet\Proxmox\Cpus\Domain\Reponses;

final readonly  class CpuResponse
{

    public function __construct(private string $vendor, private string $name, private int $custom  )
    {
    }

    public function GetVendor():string
    {
        return $this->vendor;
    }

    public function GetName():string
    {
        return $this->name;
    }

    public function GetCustom():int
    {
        return $this->custom;
    }
}