<?php
declare(strict_types=1);
namespace Ginernet\Proxmox\Networks\Domain\Responses;

final class NetworksResponse
{
    private readonly  array $networks;

    public function __construct(NetworkResponse ...$networks){
        $this->networks = $networks;
    }

    public function networks():array
    {
        return  $this->networks;
    }

}