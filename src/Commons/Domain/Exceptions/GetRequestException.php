<?php
declare(strict_types=1);
namespace Ginernet\Proxmox\Commons\Domain\Exceptions;

final class GetRequestException  extends  \Exception
{
    public function __construct(string $message)
    {
        parent::__construct("Get failed ->".$message, 401);
    }
}