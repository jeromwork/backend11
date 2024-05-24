<?php

namespace Modules\Orders\Services\PayProviders;

use App\Models\User;
use Modules\Orders\DataStructures\CartStructure;
use Modules\Orders\DataStructures\ClientStructure;
use Modules\Orders\Models\Order;

interface PayProviderInterface
{
    public function forOrder(Order $order):self;
    public function forCart(CartStructure $cartStructure):self;
    public function forUser(User $user):self;

}
