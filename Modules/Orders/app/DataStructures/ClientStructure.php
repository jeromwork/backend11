<?php

namespace Modules\Orders\DataStructures;

use App\DataStructures\AbstractDataStructure;
use Illuminate\Http\Request;
class ClientStructure extends AbstractDataStructure
{
    public int $id = 0;
    public ?string $fio = null;
    public string $phone = '';
    public string $email = '';




}
