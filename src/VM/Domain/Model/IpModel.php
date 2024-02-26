<?php
declare(strict_types=1);
namespace Ginernet\Proxmox\VM\Domain\Model;

final class IpModel
{
    private  string $text;

    public function __construct(private readonly ?int $index, private readonly ?string $ip, private readonly  ?string $gateway)
    {
        $this->text='';
    }

    public function GetIndex():?int
    {
        return $this->index;
    }

    private function GetIp():?string
    {
        return $this->ip;
    }

    private function GetGateway():?string
    {
        return $this->gateway;
    }

    public function toString():string
    {
        if($this->GetIp()) $this->text .='ip='.$this->GetIp();
        if($this->GetGateway()) $this->text .=',gw='.$this->GetGateway();
        return $this->text;
    }


}