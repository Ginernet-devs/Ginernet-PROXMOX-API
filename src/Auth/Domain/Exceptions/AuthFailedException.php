<?php
declare(strict_types=1);
namespace PromoxApiClient\Auth\Domain\Exceptions;

final class AuthFailedException extends \Error
{

    public function __construct()
    {
        parent::__construct("Auth Failed", 401);
    }
}