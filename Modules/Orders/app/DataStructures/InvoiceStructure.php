<?php

namespace Modules\Orders\DataStructures;

use App\DataStructures\AbstractDataStructure;
use Illuminate\Http\Request;
class InvoiceStructure extends AbstractDataStructure
{
    public ?string $id = null;
    public ?string $url = null;
    public string $text = '';




}
