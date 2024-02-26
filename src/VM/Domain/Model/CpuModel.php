<?php
declare(strict_types=1);
namespace Ginernet\Proxmox\VM\Domain\Model;

final class CpuModel
{

    private string $text;

    public function __construct(private readonly string $cpuTypes, private readonly int $cores,
                                private readonly int $memory, private readonly  int $ballon){

        $this->text='';
    }

    public function getCpuTypes():string
    {
        return $this->cpuTypes;
    }

    public function getCores():int
    {
        return $this->cores;
    }

    public function getMemory():int
    {
        return $this->memory;
    }

    public function getBallon():int
    {
        return $this->ballon;
    }

}