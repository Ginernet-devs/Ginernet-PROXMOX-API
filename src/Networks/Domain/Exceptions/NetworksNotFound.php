<?php

namespace PromoxApiClient\Networks\Domain\Exceptions;

class NetworksNotFound extends \Error
{
    public function __construct()
    {
        parent::__construct("Networks Not Found", 204);
    }
}