<?php
declare(strict_types=1);
namespace Ginernet\Proxmox\Cluster\Domain\Exceptions;

class ClusterNotFound Extends \Exception
{
    public function __construct()
    {
        parent::__construct("Cluster Not Found", 204);
    }

}