<?php

declare(strict_types=1);

namespace Ginernet\Proxmox\VM\Domain\Exceptions;



final class GetConfigVMException extends \Exception

{
    
    public function __construct(string $message)

    {

        parent::__construct("Error Config -> ".$message, 400);

    }

}