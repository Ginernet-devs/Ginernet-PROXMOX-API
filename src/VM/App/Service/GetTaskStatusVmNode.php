<?php

declare(strict_types=1);

namespace Ginernet\Proxmox\VM\App\Service;



use Ginernet\Proxmox\Commons\Domain\Entities\Connection;

use Ginernet\Proxmox\Commons\Domain\Entities\CookiesPVE;

use Ginernet\Proxmox\Commons\infrastructure\GClientBase;
use Ginernet\Proxmox\VM\Domain\Exceptions\GetTaskStatusVMException;

class GetTaskStatusVmNode extends GClientBase

{

    public function __construct(Connection $connection, CookiesPVE $cookiesPVE)

    {

        parent::__construct($connection, $cookiesPVE);

    }



    public function __invoke(string $node, string $upid)

    {
        
        try{
            
          $result =  $this->Get("nodes/".$node."/tasks/".$upid."/status");

          return $result;

        }catch(\Exception $ex){
            if ($ex->getCode()===500) throw new GetTaskStatusVMException($ex->getMessage());
            return throw new GetTaskStatusVMException("Error in Task Status VM");
        }

        return null;

    }





}