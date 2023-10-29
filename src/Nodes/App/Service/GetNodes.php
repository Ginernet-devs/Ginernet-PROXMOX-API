<?php
declare(strict_types=1);
namespace PromoxApiClient\Nodes\App\Service;

use GuzzleHttp\Exception\GuzzleException;
use PromoxApiClient\Commons\Application\Helpers\GFunctions;
use PromoxApiClient\Commons\Domain\Entities\Connection;
use PromoxApiClient\Commons\Domain\Entities\CookiesPVE;
use PromoxApiClient\Commons\Domain\Exceptions\AuthFailedException;
use PromoxApiClient\Commons\Domain\Exceptions\HostUnreachableException;
use PromoxApiClient\Commons\infrastructure\GClientBase;
use PromoxApiClient\Nodes\Domain\Responses\NodeResponse;
use PromoxApiClient\Nodes\Domain\Responses\NodesResponse;

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
            $result = $this->Get("/nodes", []);
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