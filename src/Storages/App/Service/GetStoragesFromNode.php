<?php
declare(strict_types=1);
namespace PromoxApiClient\Storages\App\Service;

use GuzzleHttp\Exception\GuzzleException;
use PromoxApiClient\Commons\Application\Helpers\GFunctions;
use PromoxApiClient\Commons\Domain\Entities\Connection;
use PromoxApiClient\Commons\Domain\Entities\CookiesPVE;
use PromoxApiClient\Commons\Domain\Exceptions\AuthFailedException;
use PromoxApiClient\Commons\Domain\Exceptions\HostUnreachableException;
use PromoxApiClient\Commons\infrastructure\GClientBase;
use PromoxApiClient\Nodes\Domain\Responses\NodeResponse;
use PromoxApiClient\Nodes\Domain\Responses\NodesResponse;
use PromoxApiClient\Storages\Domain\Exceptions\NodeNotFound;
use PromoxApiClient\Storages\Domain\Responses\StorageResponse;
use PromoxApiClient\Storages\Domain\Responses\StoragesResponse;

final class GetStoragesFromNode  extends GClientBase
{
    use GFunctions;



    public function __construct(Connection $connection, CookiesPVE $cookiesPVE)
    {
        parent::__construct($connection, $cookiesPVE);
    }


    public function __invoke(string $node):?StoragesResponse
    {
        try {
            $result = $this->Get("/nodes/".$node."/storage", []);
            if (empty($result)) throw new NodeNotFound();
            return  new StoragesResponse(...array_map($this->toResponse(), $result));
        }catch(GuzzleException $ex){
            if ($ex->getCode() === 401) throw new AuthFailedException();
            if ($ex->getCode() === 0) throw new HostUnreachableException();
        }
        return  null;
    }

    public function toResponse():callable
    {
        return static fn($result): StorageResponse => new StorageResponse(
           $result['type'],
           $result['used'],
           $result['avail'],
           $result['total'],
           $result['enabled']===1,
           $result['storage'],
           $result['used_fraction'],
           explode(',',$result['content']),
           $result['active']===1,
           $result['shared']===1
        );
    }
}