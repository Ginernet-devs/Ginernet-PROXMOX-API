<?php
declare(strict_types=1);
namespace Ginernet\Proxmox\Storages\Domain\Exceptions;

class StoragesNotFound extends \Exception
{
    public function __construct()
    {
        parent::__construct("Storages Not Found", 204);
    }
}


