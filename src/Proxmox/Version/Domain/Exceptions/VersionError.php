<?php
declare(strict_types=1);
namespace Ginernet\Proxmox\Proxmox\Version\Domain\Exceptions;

final class VersionError extends \Error
{
    /**
     * @param string $message
     */
    public function __construct(string $message)
    {
        parent::__construct("Error in obtain version ->".$message, 400);
    }
}