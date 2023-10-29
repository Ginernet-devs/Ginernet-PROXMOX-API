<?php
declare(strict_types=1);
namespace PromoxApiClient\Nodes\Domain\Responses;

final readonly class NodeResponse
{

    public function __construct(private string $status,private string $level, private string $id, private string $ssl_fingerprint,
                                private int $maxmem, private int $disk, private int $uptime, private int $mem, private string $node,
                                private float $cpu, private int $maxcpu, private string $type, private int $maxdisk){

    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function getLevel(): string
    {
        return $this->level;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getSslFingerprint(): string
    {
        return $this->ssl_fingerprint;
    }

    public function getMaxmem(): int
    {
        return $this->maxmem;
    }

    public function getDisk(): int
    {
        return $this->disk;
    }

    public function getUptime(): int
    {
        return $this->uptime;
    }

    public function getMem(): int
    {
        return $this->mem;
    }

    public function getNode(): string
    {
        return $this->node;
    }

    public function getCpu(): float
    {
        return $this->cpu;
    }

    public function getMaxcpu(): int
    {
        return $this->maxcpu;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getMaxdisk(): int
    {
        return $this->maxdisk;
    }



}