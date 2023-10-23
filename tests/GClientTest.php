<?php
declare(strict_types=1);
namespace Tests;
use PHPUnit\Framework\TestCase;
use PromoxApiClient\Auth\Domain\Responses\LoginResponse;
use PromoxApiClient\Commons\Domain\Exceptions\AuthFailedException;
use PromoxApiClient\Commons\Domain\Exceptions\HostUnreachableException;
use PromoxApiClient\GClient;

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


    public function testGetNodesOK():void
    {
        $client = new GClient($_ENV['HOST'],$_ENV['USERNAME'],$_ENV['PASSWORD'],$_ENV['REALM']);
        $auth = $client->login();
        $result = $client->GetNodes($auth->getCookies(),$_ENV['HOST'],8006);
        $this->assertCount(1,$result);
    }
}