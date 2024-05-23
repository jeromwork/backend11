<?php

namespace Modules\Orders\DataStructures;
use Illuminate\Support\Collection;
class CartStructure extends \App\DataStructures\AbstractDataStructure
{
    public ?Collection $goods = null;
    public ?float $sum = null;
    public int $count = 0;


}
