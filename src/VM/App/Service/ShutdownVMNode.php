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



class ShutdownVMNode extends GClientBase

{

    use GFunctions;



    public function __construct(Connection $connection, CookiesPVE $cookiesPVE)

    {

        parent::__construct($connection, $cookiesPVE);

    }


    /**
     * @param string $node
     * @param int $vmid
     * @return string|PostRequestException|VmErrorDestroy|null
     * @throws VmErrorDestroy
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

            $result = $this->Post("nodes/" . $node . "/qemu/" . $vmid . "/status/shutdown", $body);
            return  $result->getBody()->getContents();

        }catch (PostRequestException $e ) {

            if ($e->getCode()===500) throw new VmErrorDestroy($e->getMessage());

            throw new VmErrorDestroy("Error in create VM");

        }

    }

}