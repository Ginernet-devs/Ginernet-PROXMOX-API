<?php
declare(strict_types=1);
namespace Ginernet\Proxmox\VM\Domain\Model;

final class EfiModel
{
    public function __construct(private readonly ?string $storage, private readonly ?int $key)
    {
    }

    public function getStorage():?string
    {
        return $this->storage;
    }

    public function getKey():?int
    {
        return $this->key;
    }
}