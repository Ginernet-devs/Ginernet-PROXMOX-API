<?php
declare(strict_types=1);
namespace Ginernet\Proxmox\VM\Domain\Model;

final readonly class NetModel
{

    public function __construct(private int $index, private string $model, private string $bridge)
    {
    }

    public function GetIndex():int
    {
        return $this->index;
    }

    public function GetModel():string
    {
        return $this->model;
    }

    public function GetBridge():string{
        return $this->bridge;
    }

}