<?php
declare(strict_types=1);
namespace PromoxApiClient\Networks\Domain\Responses;

final readonly  class NetworkResponse
{

    public function __construct(private string $method, private string $bridge_fd, private bool $active, private string $iface,
                                private int $priority, private string $type, private bool $autostart, private string $method6,
                                private string $bridge_stp, private string $netmask, private string $cidr, private string $bridge_ports,
                                private string $gateway, private array $families, private string $address){}

    public function getMethod(): string
    {
        return $this->method;
    }

    public function getBridgeFd(): string
    {
        return $this->bridge_fd;
    }

    public function getActive(): bool
    {
        return $this->active;
    }

    public function getIface(): string
    {
        return $this->iface;
    }

    public function getPriority(): int
    {
        return $this->priority;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getAutostart(): bool
    {
        return $this->autostart;
    }

    public function getMethod6(): string
    {
        return $this->method6;
    }

    public function getBridgeStp(): string
    {
        return $this->bridge_stp;
    }

    public function getNetmask(): string
    {
        return $this->netmask;
    }

    public function getCidr(): string
    {
        return $this->cidr;
    }

    public function getBridgePorts(): string
    {
        return $this->bridge_ports;
    }

    public function getGateway(): string
    {
        return $this->gateway;
    }

    public function getFamilies(): array
    {
        return $this->families;
    }

    public function getAddress(): string
    {
        return $this->address;
    }








}