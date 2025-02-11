<?php

declare(strict_types=1);

namespace Ginernet\Proxmox\VM\Domain\Exceptions;



final class GetTaskStatusVMException extends \Exception

{

    public function __construct(string $message)

    {

        parent::__construct("Error Task Status -> ".$message, 400);

    }

}