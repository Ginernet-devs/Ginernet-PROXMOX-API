<?php

declare(strict_types=1);

namespace Ginernet\Proxmox\VM\App\Service;


use Ginernet\Proxmox\Commons\Application\Helpers\GFunctions;

use Ginernet\Proxmox\Commons\Domain\Entities\Connection;

use Ginernet\Proxmox\Commons\Domain\Entities\CookiesPVE;
use Ginernet\Proxmox\Commons\Domain\Exceptions\NotFoundSOException;
use Ginernet\Proxmox\Commons\Domain\Exceptions\PostRequestException;

use Ginernet\Proxmox\Commons\infrastructure\GClientBase;
use Ginernet\Proxmox\VM\App\Service\Help\So\SoVm;
use Ginernet\Proxmox\VM\Domain\Exceptions\VmErrorCreate;

use Ginernet\Proxmox\VM\Domain\Responses\VmResponse;

use Ginernet\Proxmox\VM\Domain\Responses\VmsResponse;



final class CreateVm extends  GClientBase
{

    use GFunctions;


    public function __construct(Connection $connection, CookiesPVE $cookiesPVE)
    {
        parent::__construct($connection, $cookiesPVE);
    }


    
    public function __invoke(
                                string  $nodeName, int $vmId, ?int $vmCpuCores, ?string $vmName, ?int $vmNetId,
                                ?string $vmNetModel, ?string $vmNetBridge, ?int $vmNetFirewall, ?bool $vmOnBoot,
                                ?string $vmScsiHw, ?string $vmDiskType, ?int    $vmDiskId, ?string $vmDiskStorage,
                                ?string $vmDiskDiscard, ?string $vmDiskCache, ?string $vmDiskImportFrom, ?string $vmTags,
                                ?int    $vmCloudInitIdeId, ?string $vmCloudInitStorage, ?string $vmBootOrder, ?int $vmAgent,
                                ?int    $vmNetNetId, ?string $vmNetIp, ?string $vmNetGw, ?string $vmOsUserName,
                                ?string $vmOsPassword, ?string $vmCpuType, ?int $vmMemory = null, ?int $vmMemoryBallon = null,
                                ?string $vmOsType = null,?string $vmBios = null,?string $vmMachinePc = null,
                                ?string $vmEfiStorage = null, ?int $vmEfiKey = null,
                                ?string $efidiskNvme = null, ?string $efidiskEnrroled = null,
                                ?string $tpmstateNvme = null, ?string $tpmstateVersion = null,
                                ?string $soBuild = 'Debian12'
                            ):?VmsResponse

    {
        $soClass = SoVm::get($soBuild);
        if ( $soClass instanceof NotFoundSOException) {
            return new NotFoundSOException($soBuild);
        }
        $so = new $soClass(
                            $nodeName, $vmId, $vmCpuCores, $vmName, $vmNetId,
                            $vmNetModel, $vmNetBridge, $vmNetFirewall, $vmOnBoot,
                            $vmScsiHw, $vmDiskType, $vmDiskId, $vmDiskStorage,
                            $vmDiskDiscard, $vmDiskCache, $vmDiskImportFrom, $vmTags,
                            $vmCloudInitIdeId, $vmCloudInitStorage, $vmBootOrder, $vmAgent,
                            $vmNetNetId, $vmNetIp, $vmNetGw, $vmOsUserName,
                            $vmOsPassword, $vmCpuType, $vmMemory, $vmMemoryBallon,
                            $vmOsType, $vmBios,$vmMachinePc,
                            $vmEfiStorage, $vmEfiKey,
                            $efidiskNvme, $efidiskEnrroled,
                            $tpmstateNvme, $tpmstateVersion
                          );
            
            
            $body = $so->buildData();

            try {
                $result = $this->Post("nodes/".$nodeName."/qemu/", $body);
                $getContent = json_decode($result->getBody()->getContents());

                $vmResponses = array_map($this->toResponse(), (array)$getContent);
                $vmResponsesNumeric = array_values($vmResponses);
                return new VmsResponse(...$vmResponsesNumeric);

            }catch (PostRequestException $e ){
                if ($e->getCode()===500) {
                    throw new VmErrorCreate($e->getMessage());
                }
                throw new VmErrorCreate("Error in create VM");
            }
    }



    public function toResponse():callable
    {
        return static fn($result):VmResponse=>new VmResponse(
            $result
        );
    }

}
