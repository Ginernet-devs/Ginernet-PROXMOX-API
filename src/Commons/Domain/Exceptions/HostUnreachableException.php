<?php
declare(strict_types=1);
namespace Ginernet\Proxmox\Commons\Domain\Exceptions;

final class HostUnreachableException extends \Exception
{
    public function __construct()
    {
        parent::__construct("Host Unreachable", 401);
    }
}