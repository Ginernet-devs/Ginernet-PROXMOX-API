<?php

declare(strict_types=1);

namespace Ginernet\Proxmox\VM\Domain\Model;



use Ginernet\Proxmox\VM\Domain\Model\Storage\StorageInterface;



final class  TpmstateModel

{



    private string $text;

    private ?int    $index;
    private ?string $file = null;
    private ?string $importFrom = null;
    private ?string $size = null;
    private ?string $version = null;
    private ?string $storage = null;



    public function __construct( ?int $index, ?string $storage = null, ?string $file = null, ?string $importFrom = null, ?string $size = null, ?string $version = null)

    {

        $this->storage = $storage;

        $this->index = $index;

        $this->file = $file;

        $this->version = $version;

        $this->importFrom = $importFrom;

        $this->size = $size;

        $this->text='';

    }



    public function GetIndex(): ?int

    {

        return $this->index;

    }

    public function GetFile(): ?string

    {

        return $this->file;

    }

    public function GetStorage(): ?string

    {

        return $this->storage;

    }

    public function GetImportFrom():?string

    {

        return $this->importFrom;

    }

    public function GetSize():?string

    {

        return $this->size;

    }

    public function GetVersion():?string

    {

        return $this->version;

    }



    public function toString():?string{

        if($this->GetFile()){ 

            if($this->text != '') $this->text .= ',';
            $this->text .= 'file=' . $this->GetFile();
        }

        if($this->GetImportFrom()){ 

            if($this->text != '') $this->text .= ',';
            $this->text .="import-from=".$this->GetImportFrom();
        }

        if($this->GetSize()){ 

            if($this->text != '') $this->text .= ',';
            $this->text .="size=".$this->GetSize();
        }

        if($this->GetStorage() ){ 

            if($this->text != '') $this->text .= ',';
            $this->text .=$this->GetStorage().":".$this->GetIndex();
        }

        if($this->GetVersion() ){ 

            if($this->text != '') $this->text .= ',';
            $this->text .="version=".$this->GetVersion();
        }



        //return "nvme:0,version=v2.0";
        return $this->text;

    }



}