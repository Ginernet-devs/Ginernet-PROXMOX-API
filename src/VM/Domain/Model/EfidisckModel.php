<?php

declare(strict_types=1);

namespace Ginernet\Proxmox\VM\Domain\Model;



use Ginernet\Proxmox\VM\Domain\Model\Storage\StorageInterface;



final class  EfidisckModel

{


    private string $text;

    private ?int    $index;
    private ?string $file = null;
    private ?string $efitype = null;
    private ?string $format = null;
    private ?string $preEnrolledKeys = null;
    private ?string $importFrom = null;
    private ?string $size = null;
    private ?string $storage = null;



    public function __construct( ?int $index, ?string $file = null, ?string $storage = null, ?string $efitype = null, ?string $format = null, ?string $preEnrolledKeys = null, ?string $importFrom = null, ?string $size = null)

    {

        $this->storage = $storage;

        $this->index = $index;

        $this->file = $file;

        $this->efitype = $efitype;

        $this->format = $format;

        $this->preEnrolledKeys = $preEnrolledKeys;

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

    public function GetEfitype(): ?string

    {

        return $this->efitype;

    }



    public function GetFormat(): ?string

    {

        return $this->format;

    }



    public function GetPreEnrolledKeys(): ?string

    {

        return $this->preEnrolledKeys;

    }



    public function GetImportFrom():?string

    {

        return $this->importFrom;

    }

    public function GetSize():?string

    {

        return $this->size;

    }
    


    public function toString():?string{

        //                       --efidisk0 nvme:0,pre-enrolled-keys=1
        if($this->GetFile()) $this->text .= 'file=' . $this->GetFile();


        if($this->GetStorage()){

            if($this->text != '') $this->text .= ',';
            $this->text .= $this->GetStorage().":".$this->GetIndex();
        }

        if($this->GetEfitype()){ 

            if($this->text != '') $this->text .= ',';
            $this->text .= 'efitype=' . $this->GetEfitype();
        }

        if($this->GetFormat()){

            if($this->text != '') $this->text .= ',';
            $this->text .="format=".$this->GetFormat();
        }

        if($this->GetImportFrom()){ 

            if($this->text != '') $this->text .= ',';
            $this->text .="import-from=".$this->GetImportFrom();
        }

        if($this->GetPreEnrolledKeys()){ 

            if($this->text != '') $this->text .= ',';
            $this->text .="pre-enrolled-keys=".$this->GetPreEnrolledKeys();
        }

        if($this->GetSize()){ 

            if($this->text != '') $this->text .= ',';
            $this->text .="size=".$this->GetSize();
        }

        return $this->text;
    }



}