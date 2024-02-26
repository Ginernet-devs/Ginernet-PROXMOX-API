<?php
declare(strict_types=1);
namespace Ginernet\Proxmox\VM\Domain\Exceptions;

final class ResizeVMDiskException extends \error
{
    public function __construct(string $message)
    {
        parent::__construct("Error Resize Disk->".$message, 400);
    }
}