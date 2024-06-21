<?php
declare(strict_types=1);
namespace Ginernet\Proxmox\VM\App\Service;

use Ginernet\Proxmox\Commons\Domain\Entities\Connection;
use Ginernet\Proxmox\Commons\Domain\Entities\CookiesPVE;
use Ginernet\Proxmox\Commons\Domain\Exceptions\PutRequestException;
use Ginernet\Proxmox\Commons\infrastructure\GClientBase;
use Ginernet\Proxmox\VM\Domain\Exceptions\ResizeVMDiskException;

final class CreateConfigVMinNode extends  GClientBase
{
    public function __construct(Connection $connection, CookiesPVE $cookiesPVE)
    {
        parent::__construct($connection, $cookiesPVE);
    }

    public function __invoke(string $node, int $vmid,?int $index, ?string $discard, ?string $cache, ?string $import):?string
    {
        try {
            $body = [
                'scsi'.$index => 'discard='.$discard,
              //  'scsi'.$index => 'file=local-lvm:vm-102-disk-0,size=32',
               // 'scsi'.$index =>'cache='.$cache,
               // 'scsi'.$index =>'import-from='.$import
            ];
            $result = $this->Post("nodes/".$node."/qemu/".$vmid."/config", $body);
            if(is_null($result)) return throw new ResizeVMDiskException("Error in config VM");
            $getContent = $result->getBody()->getContents();
            $getCode = $result->getStatusCode();
            if($getCode != 'CODE200') throw  new ResizeVMDiskException($getContent);
            return $getContent;
        }catch (PutRequestException $e ){
            if ($e->getCode()===500) throw new ResizeVMDiskException($e->getMessage());
            return throw new ResizeVMDiskException("Error in create VM");
        }
    }

}