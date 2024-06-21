<?php
declare(strict_types=1);
namespace Ginernet\Proxmox\VM\App\Service;

use Ginernet\Proxmox\Commons\Application\Helpers\GFunctions;
use Ginernet\Proxmox\Commons\Domain\Entities\Connection;
use Ginernet\Proxmox\Commons\Domain\Entities\CookiesPVE;
use Ginernet\Proxmox\Commons\Domain\Exceptions\PostRequestException;
use Ginernet\Proxmox\Commons\infrastructure\GClientBase;
use Ginernet\Proxmox\VM\Domain\Exceptions\VmErrorDestroy;
use Ginernet\Proxmox\VM\Domain\Exceptions\VmErrorStop;

class DeleteVMinNode extends GClientBase
{
    use GFunctions;

    public function __construct(Connection $connection, CookiesPVE $cookiesPVE)
    {
        parent::__construct($connection, $cookiesPVE);
    }

    public function __invoke(string  $node, int $vmid):string|PostRequestException|VmErrorDestroy|null{
        try {
            $params=
                [
                    "purge"=>true,
                    "destroy-unreferenced-disks"=>true
                ];
            $result = $this->Delete("nodes/" . $node . "/qemu/" . $vmid,$params);
            return  $result->getBody()->getContents();
        }catch (PostRequestException $e ) {
            if ($e->getCode()===500) throw new VmErrorDestroy($e->getMessage());
            return throw new VmErrorDestroy("Error in create VM");
        }
    }
}