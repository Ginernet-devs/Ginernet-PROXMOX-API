<?php
declare(strict_types=1);
namespace Ginernet\Proxmox\VM\App\Service;

use Ginernet\Proxmox\Commons\Application\Helpers\GFunctions;
use Ginernet\Proxmox\Commons\Domain\Entities\Connection;
use Ginernet\Proxmox\Commons\Domain\Entities\CookiesPVE;
use Ginernet\Proxmox\Commons\Domain\Exceptions\PostRequestException;
use Ginernet\Proxmox\Commons\infrastructure\GClientBase;

use Ginernet\Proxmox\VM\Domain\Exceptions\VmErrorStop;

class StopVMinNode extends GClientBase
{
    use GFunctions;

    public function __construct(Connection $connection, CookiesPVE $cookiesPVE)
    {
        parent::__construct($connection, $cookiesPVE);
    }

    public function __invoke(string  $node, int $vmid):string|PostRequestException|VmErrorStop|null{
        try {
            $body = [
                'vmid' => $vmid
            ];
            $result = $this->Post("nodes/" . $node . "/qemu/" . $vmid . "/status/stop", $body);
            return  $result->getBody()->getContents();
        }catch (PostRequestException $e ) {
            if ($e->getCode()===500) throw new VmErrorStop($e->getMessage());
            throw new VmErrorStop("Error in create VM");
        }

    }
}