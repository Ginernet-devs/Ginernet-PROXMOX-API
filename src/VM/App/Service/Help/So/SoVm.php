<?php
declare(strict_types=1);

namespace Ginernet\Proxmox\VM\App\Service\Help\So;

use Ginernet\Proxmox\Commons\Domain\Exceptions\NotFoundSOException;
use Ginernet\Proxmox\VM\App\Service\Help\So\Linux\CreateDataForLinuxVM;
use Ginernet\Proxmox\VM\App\Service\Help\So\Windows\CreateDataWindows11VM;
use Ginernet\Proxmox\VM\Domain\Contants\SoConst;

class SoVm
{
    const SO = [
        SoConst::LINUX => CreateDataForLinuxVM::class,
        SoConst::WINDOWS => CreateDataWindows11VM::class,

        SoConst::ROUTEROS6 => CreateDataForLinuxVM::class,
        SoConst::ROUTEROS7 => CreateDataForLinuxVM::class,
        SoConst::UBUNTU24 => CreateDataForLinuxVM::class,
        SoConst::UBUNTU22 => CreateDataForLinuxVM::class,
        SoConst::UBUNTU20 => CreateDataForLinuxVM::class,
        SoConst::UBUNTU18 => CreateDataForLinuxVM::class,
        SoConst::UBUNTU16 => CreateDataForLinuxVM::class,
        SoConst::DEBIAN10 => CreateDataForLinuxVM::class,
        SoConst::DEBIAN11 => CreateDataForLinuxVM::class,
        SoConst::DEBIAN12 => CreateDataForLinuxVM::class,
        SoConst::ALMA9 => CreateDataForLinuxVM::class,
        SoConst::ALMA8 => CreateDataForLinuxVM::class,
        SoConst::CENTOS7 => CreateDataForLinuxVM::class,
        SoConst::WINDOWS11 => CreateDataWindows11VM::class,
        SoConst::WINDOWS_11 => CreateDataWindows11VM::class
    ];
    
    public static function get(string $so): ?string
    {
            try {
                
                return self::SO[$so];
    
            }catch (NotFoundSOException $e ){
    
                return new NotFoundSOException($so);
    
            }
    }
}