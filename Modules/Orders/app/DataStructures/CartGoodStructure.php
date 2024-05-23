<?php

namespace Modules\Orders\DataStructures;

class CartGoodStructure extends \App\DataStructures\AbstractDataStructure
{
    public ?string $id = null;
    public ?float $price = null;
    public ?int $count = null;


    public string $name = '';//not yet model of goods (services), use name. Then we can be use models
    public string $note = '';//not yet model of goods (services), use name. Then we can be use models
}
