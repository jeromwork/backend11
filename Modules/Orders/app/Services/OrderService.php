<?php

namespace Modules\Orders\Services;
use App\Services\Response\ResponseService;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Modules\Orders\DataStructures\InvoiceStructure;
use Modules\Orders\Entities\Order;
use Modules\Orders\Entities\Purchase;
use Modules\Orders\Http\Requests\StoreOrderRequest;
use Modules\Orders\DataStructures\CartGoodStructure;
use Modules\Orders\DataStructures\CartStructure;
use Modules\Orders\Services\PayProviders\Paykeeper;


class OrderService
{
    protected ?StoreOrderRequest $request = null;
    protected ?CartStructure $cart = null;
    protected ?Order $order = null;



    public function createFromRequest( Request $request ):?Order    {
        //get client from request (jwt)
        if( !$user = auth()->user() )  {
            throw new \Exception('Not authorization' ); //todo @telegram errors
        }

        //ready cart service for handle cart data
        $cartService = (new CartService())->fromRequest($request);

        if(!$sum = $cartService->getSum()) {
            throw new \Exception('Incorrect cart structure'); //todo @telegram errors
        }
        $order = Order::create([
            'contacts' => json_encode(['fio' => $user->fio(), 'phone' => $user->phone_number],JSON_UNESCAPED_UNICODE ),
            'client_id' => $user->id,
            'sum' => $sum,
            'note' => $request->input('note'),
            'is_online' => true, //for offline order, neccesarelly add logic
            'payment_provider' => 'paykeeper', //todo set provider dynamically
            'status' => 'waitInvoice'
        ]);
        if(!$order) {
            throw new \Exception('Error to save order'); //todo @telegram errors
        }

        //save purchases (cart)
        $cartService->withOrder($order)->saveCart();

        //get invoice
//        $invoice = new InvoiceStructure([
//            'id' => 'f2ffsfw',
//            'url' => 'sd32fvsdfvbw',
//            'text' => 'f3f2f2qfffff3f',
//        ]);
        $invoice = (new Paykeeper())->forOrder($order)
            ->forCart($cartService->getCart())
            ->forUser($user)
            ->createInvoice();

        if (!$invoice) {
            throw new \Exception('Error to create invoice pay provider'); //todo @telegram errors
        } //todo @telegram errors

        $order->update([
            'status' => 'waitPay',
            'pay_url' => $invoice->url,
            'pay_id' => $invoice->id,
        ]);
        return $order;
    }
}
