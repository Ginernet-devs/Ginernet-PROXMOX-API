<?php
declare(strict_types=1);
namespace Ginernet\Proxmox\Networks\Domain\Exceptions;

class NetworksNotFound extends \Error
{
    public function __construct()
    {
        parent::__construct("Networks Not Found", 204);
    }
}