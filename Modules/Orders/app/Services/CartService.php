<?php

namespace Modules\Orders\Services;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Modules\Orders\Entities\Order;
use Modules\Orders\Entities\Purchase;
use Modules\Orders\Http\Requests\StoreOrderRequest;
use Modules\Orders\DataStructures\CartGoodStructure;
use Modules\Orders\DataStructures\CartStructure;



class CartService
{
    protected ?StoreOrderRequest $request = null;
    protected ?CartStructure $cart = null;
    protected ?Order $order = null;


    public function fromRequest( Request $request ):self    {
        $this->prepareCartFromRequest($request);
        return $this;
    }

    public function getGoods():?Collection    {
        return (!$this->cart) ? $this->cart->goods : null;
    }

    public function getSum():?float    {
        return ($this->cart) ? $this->cart->sum : null;
    }

    protected function prepareCartFromRequest(StoreOrderRequest $request):self {
        $goods = collect();
        $sum = 0;
        $count = 0;
        if ($cart = $request->input('cart') ){
            //todo check goods - services in model (price, info ...)
            if(isset($cart['goods'])){
                foreach ($cart['goods'] as $good){
                    if(!isset($good['id']) || !isset($good['count']) || !isset($good['price'])) continue;
                    $goods->push(new CartGoodStructure([
                        'price' => (int) $good['price'],
                        'count' => (int) $good['count'],
                        'name' => $good['name'], // No need to cast to int if it's a string
                        'id' => (int) $good['id'],
                    ]));
                    $sum += $good['price'] * $good['count'];
                    $count += $good['count'];
                }
            }
        }
        if(!$sum)    return $this;
        $this->cart = new CartStructure(['sum' => $sum, 'count' => $count, 'goods' => $goods]);
        return $this;
    }

    public function withOrder(Order $order ):self    {
        $this->order = $order;
        return $this;
    }

    public function saveCart():bool    {

        if(!$this->order ||!$this->cart) throw new \Exception('Not set order and cart for save cart');
        try {
            //save purchases with order id
            foreach ($this->cart->goods as $good){
                /** @var CartGoodStructure $good **/
                Purchase::create( $good->toArray() + ['order_id' => $this->order->id]);
            }
        }catch (\Exception $e){
            throw new \Exception('Error to save purchases: '.$e->getMessage()); //todo @telegram errors
        }

        return true;
    }

    public function getCart():?CartStructure    {
        if (!$this->cart) throw new \Exception('Not set data for working cart service');
        return $this->cart;
    }
}
