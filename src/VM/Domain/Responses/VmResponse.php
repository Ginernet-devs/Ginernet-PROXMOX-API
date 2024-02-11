<?php
declare(strict_types=1);
namespace Ginernet\Proxmox\VM\Domain\Responses;

final readonly class VmResponse
{

    public function __construct(private string $data)
    {
    }

    public function getData():string{
        return $this->data;
    }
}