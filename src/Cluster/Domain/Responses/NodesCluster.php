<?php
declare(strict_types=1);
namespace Ginernet\Proxmox\Cluster\Domain\Responses;

final readonly class NodesCluster
{
    public function __construct(private string $name, private string $ip, private string $type,
                                private string $level, private bool $nodeId, private int $local,
                                private int $online, private string $id){

    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getIp(): string
    {
        return $this->ip;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getLevel(): string
    {
        return $this->level;
    }

    public function getNodeId(): bool
    {
        return $this->nodeId;
    }

    public function getLocal(): int
    {
        return $this->local;
    }

    public function getOnline(): int
    {
        return $this->online;
    }

    public function getId(): string
    {
        return $this->id;
    }


}