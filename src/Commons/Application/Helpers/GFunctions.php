<?php
declare(strict_types=1);
namespace Ginernet\Proxmox\Commons\Application\Helpers;

use GuzzleHttp\Cookie\CookieJar;
use GuzzleHttp\Psr7\Response;

trait GFunctions
{
    function decodeBody(Response $data):array{
        return json_decode($data->getBody()->getContents(), true)['data'];
    }
    public function getCookies(string $ticket, string $host): CookieJar
    {
        return CookieJar::fromArray(
            ['PVEAuthCookie' => $ticket], $host);
    }
}