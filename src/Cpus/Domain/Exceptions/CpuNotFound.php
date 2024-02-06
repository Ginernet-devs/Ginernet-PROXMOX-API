<?php
declare(strict_types=1);
namespace Ginernet\Proxmox\Cpus\Domain\Exceptions;

class CpuNotFound extends  \Error
{
    public function __construct()
    {
        parent::__construct("Cpu Not Found", 204);
    }

}