<?php
declare(strict_types=1);
namespace PromoxApiClient\Storages\Domain\Responses;

final class StoragesResponse
{
    private readonly array   $storages;
    public function  __construct(StorageResponse ...$storages){
        $this->storages = $storages;
    }

    public function storage():array{
        return  $this->storages;
    }


}