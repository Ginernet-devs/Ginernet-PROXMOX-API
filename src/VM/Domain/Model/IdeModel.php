<?php
declare(strict_types=1);
namespace Ginernet\Proxmox\VM\Domain\Model;

final readonly  class IdeModel
{
    public function __construct(private int $index, private string $file)
    {

    }
    public function GetIndex():int
    {
        return $this->index;
    }

    public function GetFile():string
    {
        return $this->file;
    }


}