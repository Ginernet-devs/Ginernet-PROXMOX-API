<?php
declare(strict_types=1);
namespace Ginernet\Proxmox\VM\Domain\Model;

final class  ScsiModel
{

    private string $text;

    public function __construct(private readonly ?int    $index, private readonly ?int $main, private readonly ?string $discard, private readonly ?string $cache,
                                private readonly ?string $importFrom)
    {
        $this->text='';
    }

    public function GetIndex(): ?int
    {
        return $this->index;
    }

    private function GetMain(): ?int
    {
        return $this->main;
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

        if(empty($this->GetMain())) $this->text  .= "file=main:".$this->GetMain();
        if($this->GetDiscard()) $this->text .=",discard=".$this->GetDiscard();
        if($this->GetCache()) $this->text .=",cache=".$this->GetCache();
        if($this->GetImportFrom()) $this->text .=",import-from=".$this->GetImportFrom();
        return $this->text;
    }

}