<?php

declare(strict_types=1);

namespace Ginernet\Proxmox\VM\App\Service;



use Ginernet\Proxmox\Commons\Domain\Entities\Connection;

use Ginernet\Proxmox\Commons\Domain\Entities\CookiesPVE;

use Ginernet\Proxmox\Commons\infrastructure\GClientBase;
use Ginernet\Proxmox\VM\Domain\Exceptions\GetStatusVMException;

class GetStatusVMinNode extends GClientBase

{

    public function __construct(Connection $connection, CookiesPVE $cookiesPVE)

    {

        parent::__construct($connection, $cookiesPVE);

    }



    public function __invoke(string $node, int $vmid, bool $current = false):?array

    {

        try{

            ($current)? $getCurrent = '/current' : $getCurrent = '';
            $result =  $this->Get("nodes/".$node."/qemu/".$vmid."/status" . $getCurrent );

          return $result;

        }catch(\Exception $ex){
            if ($ex->getCode()===500) throw new GetStatusVMException($ex->getMessage());
            return throw new GetStatusVMException("Error in Status VM");



        }

        return null;

    }





}