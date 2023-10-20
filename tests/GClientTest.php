<?php
declare(strict_types=1);
namespace Tests;
use PromoxApiClient\Auth\Domain\Responses\LoginResponse;
use PromoxApiClient\GClient;
use PHPUnit\Framework\TestCase;

class GClientTest extends  TestCase
{


    public function testLoginCLient():void
    {
        $client = new GClient($_ENV['HOST'],$_ENV['USERNAME'],$_ENV['PASSWORD'],$_ENV['REALM']);
        $result = $client->login();
        $this->assertInstanceOf(LoginResponse::class, $result);
    }
}