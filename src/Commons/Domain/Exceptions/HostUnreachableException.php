<?php
declare(strict_types=1);
namespace Ginernet\Proxmox\Commons\Domain\Exceptions;

final class HostUnreachableException extends \Error
{
    public function __construct()
    {
        parent::__construct("Host Unreachable", 404);
    }
}