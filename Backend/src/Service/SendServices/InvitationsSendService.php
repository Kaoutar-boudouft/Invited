<?php

namespace App\Service\SendServices;

class InvitationsSendService
{

    private int $contactsId;

    public function __construct(int $contactsId){
        $this->contactsId = $contactsId;
    }

    public function getContacts(): int
    {
        return $this->contactsId;
    }
}