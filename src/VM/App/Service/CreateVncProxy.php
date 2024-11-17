<?php
declare(strict_types=1);
namespace Ginernet\Proxmox\VM\App\Service;

use Ginernet\Proxmox\Commons\Domain\Entities\Connection;
use Ginernet\Proxmox\Commons\Domain\Entities\CookiesPVE;
use Ginernet\Proxmox\Commons\Domain\Exceptions\PostRequestException;
use Ginernet\Proxmox\Commons\infrastructure\GClientBase;
use Ginernet\Proxmox\VM\Domain\Exceptions\VncProxyError;
use Ginernet\Proxmox\VM\Domain\Responses\VncResponse;

final class  CreateVncProxy extends GClientBase
{
    public function __construct(Connection $connection, CookiesPVE $cookiesPVE)
    {
        parent::__construct($connection, $cookiesPVE);
    }


    public function __invoke(string $node, int $vmid):?VncResponse
    {
        try {
            $body=[
            "generate-password"=>true,
             "websocket"=>true
            ];
            $result = $this->Post("nodes/" . $node . "/qemu/" . $vmid.'/vncproxy',$body);
            return $this->toResponse(json_decode($result->getBody()->getContents(),true));
        }catch (PostRequestException $e ){
                if ($e->getCode()===500) throw new VncProxyError($e->getMessage());
                return throw new VncProxyError("Error in create VM ->".$e->getMessage());

        }
    }

    public function toResponse($result):VncResponse
    {
        return new VncResponse(
            $result['data']['password'],
            $result['data']['cert'],
            $result ['data']['upid'],
            $result['data']['port'],
            $result['data']['user'],
            $result['data']['ticket']
        );
    }
}