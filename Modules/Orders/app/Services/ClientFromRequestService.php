<?php

namespace Modules\Orders\Services;

use Illuminate\Http\Request;
use Modules\Orders\DataStructures\ClientStructure;

class ClientFromRequestService
{
    protected ?ClientStructure $clientStructure = null;

    public function __construct(Request $request)
    {
        //if client exists by jwt, get his info
        $user = auth()->user();
        $fio = $phone = $email = '';
        if ( $user )    {
            $fio = "$user->surname $user->name $user->lastname";
            $phone = $user->phone_number;
            $email = $user->email;
        }
        else {
            if ($request->input('fio')) $fio = $request->input('fio');
            if ($request->input('phone')) $phone = $request->input('phone');
            if ($request->input('email')) $email = $request->input('email');
        }

        if(!$fio || (!$phone && !$email)) throw new \Exception('Not defined fio for client');
        //todo @telegram error
        $this->clientStructure = new ClientStructure([
            'id' => ($user->id) ?? 0,
           'fio' => $fio,
           'phone' => $phone,
           'email' => $email,
        ]);

    }

    public function getClient():?ClientStructure    {
        return $this->clientStructure;
    }
}
