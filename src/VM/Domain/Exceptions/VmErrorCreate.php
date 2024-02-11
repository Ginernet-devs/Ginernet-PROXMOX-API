<?php
declare(strict_types=1);
namespace Ginernet\Proxmox\VM\Domain\Exceptions;

final class VmErrorCreate extends \error
{
    public function __construct(string $message)
    {
        parent::__construct("Error create VM".$message, 400);
    }
}