<?php
declare(strict_types=1);
namespace Ginernet\Proxmox\Cpus\Domain\Exceptions;

class CpuNotFound extends  \Exception
{
    public function __construct()
    {
        parent::__construct("Cpu Not Found", 204);
    }

}