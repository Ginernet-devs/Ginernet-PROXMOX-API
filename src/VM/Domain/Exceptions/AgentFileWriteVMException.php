<?php

declare(strict_types=1);

namespace Ginernet\Proxmox\VM\Domain\Exceptions;



final class AgentFileWriteVMException extends \Exception

{

    public function __construct(string $message)

    {

        parent::__construct("Error Agent File Write ->".$message, 400);

    }

}