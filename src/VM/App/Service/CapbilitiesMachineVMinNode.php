<?php

declare(strict_types=1);

namespace Ginernet\Proxmox\VM\App\Service;



use Ginernet\Proxmox\Commons\Domain\Entities\Connection;

use Ginernet\Proxmox\Commons\Domain\Entities\CookiesPVE;

use Ginernet\Proxmox\Commons\infrastructure\GClientBase;



class CapbilitiesMachineVMinNode extends GClientBase

{

    public function __construct(Connection $connection, CookiesPVE $cookiesPVE)

    {

        parent::__construct($connection, $cookiesPVE);

    }



    public function __invoke(string $node)

    {
        
        try{


          $params = [
            'node' => $node,
          ];
          $result =  $this->Get("nodes/".$node."/capabilities/qemu/machines");

          return $result;

        }catch(\Exception $ex){



        }

        return null;

    }





}