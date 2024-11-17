<?php
declare(strict_types=1);
namespace Ginernet\Proxmox\Nodes\App\Service;

use GuzzleHttp\Exception\GuzzleException;
use Ginernet\Proxmox\Commons\Application\Helpers\GFunctions;
use Ginernet\Proxmox\Commons\Domain\Entities\Connection;
use Ginernet\Proxmox\Commons\Domain\Entities\CookiesPVE;
use Ginernet\Proxmox\Commons\Domain\Exceptions\AuthFailedException;
use Ginernet\Proxmox\Commons\Domain\Exceptions\HostUnreachableException;
use Ginernet\Proxmox\Commons\infrastructure\GClientBase;
use Ginernet\Proxmox\Nodes\Domain\Responses\NodeResponse;
use Ginernet\Proxmox\Nodes\Domain\Responses\NodesResponse;

final class GetNodes extends GClientBase
{
    use GFunctions;



    public function __construct(Connection $connection, CookiesPVE $cookiesPVE)
    {
        parent::__construct($connection, $cookiesPVE);
    }


    public function __invoke():?NodesResponse
    {
        try {
            $result = $this->Get("nodes", []);
            return new NodesResponse(...array_map($this->toResponse(), $result));
        }catch(GuzzleException $ex){
            if ($ex->getCode() === 401) throw new AuthFailedException();
            if ($ex->getCode() === 0) throw new HostUnreachableException();
        }
        return  null;
    }

    public function toResponse():callable
    {
        return static fn($result): NodeResponse => new NodeResponse(
            $result['status'],
            $result['level'],
            $result['id'],
            $result['ssl_fingerprint'],
            $result['maxmem'],
            $result['disk'],
            $result['uptime'],
            $result['mem'],
            $result['node'],
            $result['cpu'],
            $result['maxcpu'],
            $result['type'],
            $result['maxdisk'],
        );
    }
}