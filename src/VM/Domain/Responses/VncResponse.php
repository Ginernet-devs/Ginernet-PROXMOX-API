<?php
declare(strict_types=1);
namespace Ginernet\Proxmox\VM\Domain\Responses;

final  readonly class VncResponse
{
    public function __construct(private string $password,private string $cert,private string $upid,private string $port,private string $user,private string $ticket )
    {
    }

    public function getPassword() : string
    {
        return $this->password;
    }

    public function getCert() : string
    {
        return $this->cert;
    }

    public function getUpid() : string
    {
        return $this->upid;
    }

    public function getPort() : string
    {
        return $this->port;
    }

    public function getUser() : string
    {
        return $this->user;
    }

    public function getTicket() : string
    {
        return $this->ticket;
    }



}