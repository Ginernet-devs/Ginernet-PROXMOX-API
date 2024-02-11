<?php
declare(strict_types=1);
namespace Ginernet\Proxmox\VM\App\Service;

use Ginernet\Proxmox\Commons\Application\Helpers\GFunctions;
use Ginernet\Proxmox\Commons\Domain\Entities\Connection;
use Ginernet\Proxmox\Commons\Domain\Entities\CookiesPVE;
use Ginernet\Proxmox\Commons\infrastructure\GClientBase;

final class CreateVMinNode extends  GClientBase
{
    use GFunctions;
    public function __construct(Connection $connection, CookiesPVE $cookiesPVE)
    {
        parent::__construct($connection, $cookiesPVE);
    }

    public function __invoke(string $node, int $vmid, ?int $cores, ?string $name)
    {

        try {
            $body = [
                'vmid' => $vmid,
                'cores' => $cores,
                'name' => $name
            ];

            $result = $this->Post("/nodes/".$node."/qemu/", $body);
            var_dump("Resultado".$result);
        }catch (\Exception $e ){
            var_dump("Error".$e);
        }
    }
}