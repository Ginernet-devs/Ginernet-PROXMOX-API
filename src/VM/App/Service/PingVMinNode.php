<?php

declare(strict_types=1);

namespace Ginernet\Proxmox\VM\App\Service;



use Ginernet\Proxmox\Commons\Domain\Entities\Connection;

use Ginernet\Proxmox\Commons\Domain\Entities\CookiesPVE;

use Ginernet\Proxmox\Commons\infrastructure\GClientBase;



class PingVMinNode extends GClientBase

{

    public function __construct(Connection $connection, CookiesPVE $cookiesPVE)

    {

        parent::__construct($connection, $cookiesPVE);

    }



    public function __invoke(string $node, int $vmid)

    {
        
        try{


          $data = [
                    'vmid' => $vmid,
          ];
          $result =  $this->Post("nodes/".$node."/qemu/".$vmid."/agent/ping", $data);

          return $result->getBody()->getContents();

        }catch(\Exception $ex){



        }

        return null;

    }





}