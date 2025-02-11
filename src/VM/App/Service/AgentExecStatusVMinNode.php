<?php

declare(strict_types=1);

namespace Ginernet\Proxmox\VM\App\Service;



use Ginernet\Proxmox\Commons\Domain\Entities\Connection;

use Ginernet\Proxmox\Commons\Domain\Entities\CookiesPVE;

use Ginernet\Proxmox\Commons\infrastructure\GClientBase;
use Ginernet\Proxmox\VM\Domain\Exceptions\AgentExecStatusVMException;

class AgentExecStatusVMinNode extends GClientBase

{

    public function __construct(Connection $connection, CookiesPVE $cookiesPVE)

    {

        parent::__construct($connection, $cookiesPVE);

    }



    public function __invoke(string $node, int $vmid, string $pid)

    {
        
        try{


          $params = [
            'node' => $node,
            'pid' => $pid,
            'vmid' => $vmid
          ];
          $result =  $this->Get("nodes/".$node."/qemu/".$vmid."/agent/exec-status", $params);
          return $result;

        }catch(\Exception $ex){ 
            if ($ex->getCode()===500) throw new AgentExecStatusVMException($ex->getMessage());
            return throw new AgentExecStatusVMException("Error in Agent Status VM");

        }

        return null;

    }





}