<?php
declare(strict_types=1);
namespace Tests;
use PromoxApiClient\GClient;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Dotenv\Dotenv;

class GClientTest extends  TestCase
{


    public function testLoginCLient():void
    {
        $client = new GClient($_ENV['HOST'],$_ENV['USERNAME'],$_ENV['PASSWORD'],$_ENV['REALM']);
        var_dump($client);
        $this->assertEquals(true, true);

    }
}