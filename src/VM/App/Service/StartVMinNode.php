<?php
declare(strict_types=1);
namespace Ginernet\Proxmox\VM\App\Service;

use Ginernet\Proxmox\Commons\Application\Helpers\GFunctions;
use Ginernet\Proxmox\Commons\Domain\Entities\Connection;
use Ginernet\Proxmox\Commons\Domain\Entities\CookiesPVE;
use Ginernet\Proxmox\Commons\Domain\Exceptions\PostRequestException;
use Ginernet\Proxmox\Commons\infrastructure\GClientBase;
use Ginernet\Proxmox\VM\Domain\Exceptions\ResizeVMDiskException;
use Ginernet\Proxmox\VM\Domain\Exceptions\VmErrorStart;
use Ginernet\Proxmox\VM\Domain\Responses\VmResponse;
use Ginernet\Proxmox\VM\Domain\Responses\VmsResponse;

class StartVMinNode extends GClientBase
{
    use GFunctions;

    public function __construct(Connection $connection, CookiesPVE $cookiesPVE)
    {
        parent::__construct($connection, $cookiesPVE);
    }

    public function __invoke(string  $node, int $vmid):string|PostRequestException|VmErrorStart|null{
        try {
            $body = [
                'vmid' => $vmid
            ];
            $result = $this->Post("nodes/" . $node . "/qemu/" . $vmid . "/status/start", $body);
            return  $result->getBody()->getContents();
        }catch (PostRequestException $e ) {
            if ($e->getCode()===500) throw new VmErrorStart($e->getMessage());
            return throw new VmErrorStart("Error in create VM");
        }

    }
}