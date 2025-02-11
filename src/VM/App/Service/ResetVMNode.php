<?php

declare(strict_types=1);

namespace Ginernet\Proxmox\VM\App\Service;



use Ginernet\Proxmox\Commons\Application\Helpers\GFunctions;

use Ginernet\Proxmox\Commons\Domain\Entities\Connection;

use Ginernet\Proxmox\Commons\Domain\Entities\CookiesPVE;

use Ginernet\Proxmox\Commons\Domain\Exceptions\PostRequestException;

use Ginernet\Proxmox\Commons\infrastructure\GClientBase;
use Ginernet\Proxmox\VM\Domain\Exceptions\VmErrorReset;



class ResetVMNode extends GClientBase

{

    use GFunctions;



    public function __construct(Connection $connection, CookiesPVE $cookiesPVE)

    {

        parent::__construct($connection, $cookiesPVE);

    }


    /**
     * @param string $node
     * @param int $vmid
     * @return string|PostRequestException|VmErrorReset|null
     * @throws VmErrorReset
     * @throws PostRequestException
     * 
     */
    public function __invoke(string  $node, int $vmid)
    {

        try {

            $body = [
                
                'node' => $node,
                'vmid' => $vmid

            ];

            $result = $this->Post("nodes/" . $node . "/qemu/" . $vmid . "/status/reset", $body);
            return  $result->getBody()->getContents();

        }catch (PostRequestException $e ) {

            if ($e->getCode()===500) throw new VmErrorReset($e->getMessage());

            throw new VmErrorReset("Error in reset VM");

        }

    }

}