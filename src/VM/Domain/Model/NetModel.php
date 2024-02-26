<?php
declare(strict_types=1);
namespace Ginernet\Proxmox\VM\Domain\Model;

final class NetModel
{
    private string $text;

    public function __construct(
        private readonly ?int $index,
        private readonly ?string $model,
        private readonly ?string $bridge,
        private  readonly ?int $firewall
    )
    {
        $this->text='';
    }

    public function GetIndex():?int
    {
        return $this->index;
    }

    public function GetModel():?string
    {
        return $this->model;
    }

    public function GetBridge():?string{
        return $this->bridge;
    }

    public function GetFirewall():?int
    {
        return $this->firewall;
    }
    public function toString():?string
    {
        if($this->model) $this->text .="model=". $this->GetModel();
        if($this->model) $this->text .=",bridge=". $this->GetBridge();
        if($this->model) $this->text .=",firewall=".$this->GetFirewall();
        return $this->text;
    }

}