<?php
declare(strict_types=1);
namespace Ginernet\Proxmox\Commons\Domain\Exceptions;

final class PutRequestException  extends  \Error
{
    public function __construct(string $message)
    {
        parent::__construct("Put failed ->".$message, 401);
    }
}