<?php
declare(strict_types=1);
namespace Ginernet\Proxmox\Storages\Domain\Responses;

final readonly class StorageResponse
{

    public  function __construct(private string $type, private int $used, private int $avail, private int $total, private bool $enabled, private string $storage,
                                 private float $used_fraction, private array $content, private bool $active, private bool $shared){}

    public function getType(): string
    {
        return $this->type;
    }

    public function getUsed(): int
    {
        return $this->used;
    }

    public function getAvail(): int
    {
        return $this->avail;
    }

    public function getTotal(): int
    {
        return $this->total;
    }

    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    public function getStorage(): string
    {
        return $this->storage;
    }

    public function getUsedFraction(): float
    {
        return $this->used_fraction;
    }

    public function getContent(): array
    {
        return $this->content;
    }

    public function isActive(): bool
    {
        return $this->active;
    }

    public function isShared(): bool
    {
        return $this->shared;
    }







}