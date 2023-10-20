<?php
declare(strict_types=1);
namespace Tests;
use PromoxApiClient\Auth\Domain\Exceptions\AuthFailedException;
use PromoxApiClient\Auth\Domain\Exceptions\HostUnreachableException;
use PromoxApiClient\Auth\Domain\Responses\LoginResponse;
use PromoxApiClient\GClient;
use PHPUnit\Framework\TestCase;

class GClientTest extends  TestCase
{


    public function testLoginCLientOk():void
    {
        $client = new GClient($_ENV['HOST'],$_ENV['USERNAME'],$_ENV['PASSWORD'],$_ENV['REALM']);
        $result = $client->login();
        $this->assertInstanceOf(LoginResponse::class, $result);
    }

    public function testLoginClientUserNameKO():void
    {
        $client = new GClient($_ENV['HOST'],'BRABRA',$_ENV['PASSWORD'],$_ENV['REALM']);
        $result = $client->login();
        $this->assertInstanceOf(AuthFailedException::class, $result);
    }

    public function testLoginClientPASSWORDKO():void
    {
        $client = new GClient($_ENV['HOST'],$_ENV['USERNAME'],'DFDFDF',$_ENV['REALM']);
        $result = $client->login();
        $this->assertInstanceOf(AuthFailedException::class, $result);
    }

    public function testLoginClientREALMKO():void
    {
        $client = new GClient($_ENV['HOST'],$_ENV['USERNAME'],$_ENV['PASSWORD'],'BRA');
        $result = $client->login();
        $this->assertInstanceOf(AuthFailedException::class, $result);
    }

    public function testLoginClientHOSTKO():void
    {
        $client = new GClient('bbbb',$_ENV['USERNAME'],$_ENV['PASSWORD'],$_ENV['REALM']);
        $result = $client->login();
        $this->assertInstanceOf(HostUnreachableException::class, $result);
    }
}