<?php
declare(strict_types=1);
namespace Ginernet\Proxmox\VM\App\Service;

use Exception;
use Ginernet\Proxmox\Commons\Domain\Entities\Connection;
use Ginernet\Proxmox\Commons\Domain\Entities\CookiesPVE;
use Ginernet\Proxmox\Commons\Domain\Exceptions\PutRequestException;
use Ginernet\Proxmox\Commons\infrastructure\GClientBase;
use Ginernet\Proxmox\VM\Domain\Exceptions\ResizeVMDiskException;

final class ResizeVMDisk extends GClientBase
{
    public function __construct(Connection $connection, CookiesPVE $cookiesPVE)
    {
        parent::__construct($connection, $cookiesPVE);
    }

    public function __invoke(string $node, int $vmid,?string $disk, ?string $size):?string
    {

        try {
            $body = [
                'disk' => $disk,
                'size' => $size,
            ];
            $result = $this->Put("nodes/" . $node . "/qemu/" . $vmid . "/resize", $body);
            return $result->getBody()->getContents();
       }catch (PutRequestException $e ){
            if ($e->getCode()===500) throw new ResizeVMDiskException($e->getMessage());
            return throw new ResizeVMDiskException("Error in create VM");
        }
    }
}