<?php
declare(strict_types=1);
namespace Ginernet\Proxmox\Cpus\Domain\Reponses;

final readonly class CpusResponse
{
    private array $cpus;

    public function __construct(CpuResponse ...$cpus){
        $this->cpus = $cpus;
    }

    public function cpus():array
    {
        return  $this->cpus;
    }
}