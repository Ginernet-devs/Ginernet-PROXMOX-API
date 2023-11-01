<?php
declare(strict_types=1);
namespace PromoxApiClient\Nodes\Domain\Responses;

final class NodesResponse
{
    private readonly  array $nodes;
    public function __construct(NodeResponse ...$nodes){
        $this->nodes = $nodes;
    }

    public function nodes():array
    {
        return $this->nodes;
    }

}