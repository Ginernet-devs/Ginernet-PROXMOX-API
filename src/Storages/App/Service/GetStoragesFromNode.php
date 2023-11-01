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
use PromoxApiClient\Storages\Domain\Exceptions\StoragesNotFound;
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
            if (empty($result)) throw new StoragesNotFound();
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
            (array_key_exists('type', $result))?$result['type']:"",
            (array_key_exists('used', $result))?$result['used']:0,
            (array_key_exists('avail', $result))?$result['avail']:0,
            (array_key_exists('total', $result))?$result['total']:0,
            (array_key_exists('enabled', $result)) && $result['enabled']===1,
            (array_key_exists('storage', $result))?$result['storage']:"",
            (array_key_exists("used_fraction", $result))?$result['used_fraction']:0.0,
            (array_key_exists('content', $result))?explode(',',$result['content']):[],
            array_key_exists('active', $result) && $result['active'] === 1,
           array_key_exists('shared', $result) && $result['shared']===1
        );
    }
}