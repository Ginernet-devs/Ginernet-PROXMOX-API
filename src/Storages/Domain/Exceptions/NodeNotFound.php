<?php
declare(strict_types=1);
namespace PromoxApiClient\Storages\Domain\Exceptions;

final class NodeNotFound extends \Error
{
    public function __construct()
    {
        parent::__construct("Node Not Found", 204);
    }
}


