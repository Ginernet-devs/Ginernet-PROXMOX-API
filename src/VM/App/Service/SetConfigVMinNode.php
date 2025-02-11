<?php

declare(strict_types=1);

namespace Ginernet\Proxmox\VM\App\Service;



use Ginernet\Proxmox\Commons\Domain\Entities\Connection;

use Ginernet\Proxmox\Commons\Domain\Entities\CookiesPVE;

use Ginernet\Proxmox\Commons\infrastructure\GClientBase;
use Ginernet\Proxmox\VM\Domain\Exceptions\GetConfigVMException;

class SetConfigVMinNode extends GClientBase

{

    public function __construct(Connection $connection, CookiesPVE $cookiesPVE)

    {

        parent::__construct($connection, $cookiesPVE);

    }



    public function __invoke(string $node, int $vmid, array $params)

    {
        $body = [];
        $keys_to_check = ['keyboard', 'localtime', 'memory', 'name', 'machine', 'onboot', 'ostype', 'bios'];
        $filtered_params = array_intersect_key($params, array_flip($keys_to_check));
        $body = array_merge($body, $filtered_params);
        try{



          $result =  $this->Post("nodes/".$node."/qemu/".$vmid."/config", $body);

          return $result;

        }catch(\Exception $ex){
            if ($ex->getCode()===500) throw new GetConfigVMException($ex->getMessage());
            return throw new GetConfigVMException("Error in Config VM");



        }

        return null;

    }





}