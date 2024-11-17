<?php
declare(strict_types=1);
namespace Ginernet\Proxmox\Cpus\App\Service;

use Ginernet\Proxmox\Commons\Application\Helpers\GFunctions;
use Ginernet\Proxmox\Commons\Domain\Entities\Connection;
use Ginernet\Proxmox\Commons\Domain\Entities\CookiesPVE;
use Ginernet\Proxmox\Commons\Domain\Exceptions\AuthFailedException;
use Ginernet\Proxmox\Commons\Domain\Exceptions\HostUnreachableException;
use Ginernet\Proxmox\Commons\infrastructure\GClientBase;
use Ginernet\Proxmox\Cpus\Domain\Exceptions\CpuNotFound;
use Ginernet\Proxmox\Cpus\Domain\Reponses\CpuResponse;
use Ginernet\Proxmox\Cpus\Domain\Reponses\CpusResponse;
use GuzzleHttp\Exception\GuzzleException;

final class GetCpuFromNode extends GClientBase
{
    use GFunctions;

     public function __construct(Connection $connection, CookiesPVE $cookiesPVE)
     {
         parent::__construct($connection, $cookiesPVE);
     }


    public function __invoke(string $node):?CpusResponse
    {
        try {
            $result = $this->Get("nodes/" . $node . "/capabilities/qemu/cpu", []);
            if (empty($result)) throw new CpuNotFound();
            return new CpusResponse(...array_map($this->toResponse(), $result));
        }catch (GuzzleException $ex){
            if ($ex->getCode() === 401) throw new AuthFailedException();
            if ($ex->getCode() === 0) throw new HostUnreachableException();
        }
        return  null;
    }
    public function toResponse():callable
    {
        return static fn($result): CpuResponse=>new CpuResponse(
            (array_key_exists('vendor', $result))?$result['vendor']:"",
            (array_key_exists('name', $result))?$result['name']:"",
            array_key_exists('custom', $result) ?$result['custom']:0
        );
    }

}

