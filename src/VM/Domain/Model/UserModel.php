<?php
declare(strict_types=1);

namespace Ginernet\Proxmox\VM\Domain\Model;

final class  UserModel
{

    private string $text;

    public  function __construct(private readonly string $username,  private readonly string $password){
        $this->text='';
    }


    public function GetUserName():string
    {
        return $this->username;
    }

    public function GetPassword():string
    {
        return $this->password;
    }
    public function toString():?string
    {

        if (empty($this->GetUserName())) $this->text .=  $this->GetUserName();
        if (empty($this->GetPassword())) $this->text .=' --cipassword='.$this->GetPassword();
        return $this->text;
    }


}