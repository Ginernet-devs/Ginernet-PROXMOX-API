<?php
declare(strict_types=1);
namespace PromoxApiClient\Storages\Domain\Exceptions;

final class StoragesNotFound extends \Error
{
    public function __construct()
    {
        parent::__construct("Storages Not Found", 204);
    }
}


