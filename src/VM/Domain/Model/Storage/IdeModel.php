<?php
declare(strict_types=1);
namespace Ginernet\Proxmox\VM\Domain\Model\Storage;

final class IdeModel implements StorageInterface
{
    private string $text;

    public function __construct(private readonly ?int    $index, private readonly ?string $diskStorage, private readonly ?string $discard, private readonly ?string $cache,
                                private readonly ?string $importFrom)
    {
        $this->text='';
    }

    public function GetIndex(): ?int
    {
        return $this->index;
    }

    public function GetDiskStorage(): ?string
    {
        return $this->diskStorage;
    }

    public function GetDiscard(): ?string
    {
        return $this->discard;
    }

    public function GetCache(): ?string
    {
        return $this->cache;
    }

    public function GetImportFrom():?string
    {
        return $this->importFrom;
    }

    public function toString():?string{
        if($this->GetDiskStorage()) $this->text =$this->GetDiskStorage().':0';
        if($this->GetDiscard()) $this->text .=",discard=".$this->GetDiscard();
        if($this->GetCache()) $this->text .=",cache=".$this->GetCache();
        if($this->GetImportFrom()) $this->text .=",import-from=".$this->GetImportFrom();
        return $this->text;
    }
}