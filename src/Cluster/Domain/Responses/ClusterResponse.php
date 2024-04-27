<?php
declare(strict_types=1);
namespace Ginernet\Proxmox\Cluster\Domain\Responses;

final readonly class ClusterResponse
{

    public function __construct(private ?string $type, private ?string $name, private ?int $version, private ?string $id, private ?NodesClusterResponse $nodesCluster  )
    {
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function getVersion(): ?int
    {
        return $this->version;
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getNodesCluster(): ?NodesClusterResponse
    {
        return $this->nodesCluster;
    }



}