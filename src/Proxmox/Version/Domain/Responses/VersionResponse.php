<?php
declare(strict_types=1);
namespace Ginernet\Proxmox\Proxmox\Version\Domain\Responses;

final readonly  class VersionResponse
{
    /**
     * @param string $release
     * @param string $repoid
     * @param string $version
     */
    public  function __construct(private string $release, private string $repoid, private string $version ){}

    /**
     * @return string
     */
    public function getRelease():string
    {
        return $this->release;
    }

    /**
     * @return string
     */
    public function getRepoid():string
    {
        return $this->repoid;
    }

    /**
     * @return string
     */
    public function getVersion():string
    {
        return $this->version;
    }


}