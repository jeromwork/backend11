<?php

namespace Modules\Orders\Http\Controllers\Control;

use App\Http\Controllers\Controller;
use App\Services\Response\ResponseService;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Notification;
use Modules\Orders\Http\Requests\Control\StoreOrderRequest;
use Modules\Orders\Notifications\OrderCreatedAdminNotification;
use Modules\Orders\Notifications\OrderCreatedNotification;
use Modules\Orders\Services\OrderService;

class OrdersController extends \Illuminate\Routing\Controller
{
    protected OrderService $orderService;

    public function __construct( OrderService $orderService )    {
        $this->orderService = $orderService;
    }
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index()
    {
        return view('orders::index');
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(StoreOrderRequest $request): JsonResponse
    {

        /**
         * Ордер без товаров не имеет смысла
         * Слот может иметь ордер или не иметь
         * товары в корзине знают об ордере
         * ордер не знает о товарах в корзине (модель ордера может забрать товары)
         * сервис корзины здесь в контроллере берет данные из реквест createOrderFromRequestService
         * сервис корзины в консоле берет данные из консольных переменных, createOrderFromConsoleService
         *
         */

        try {
            if( !$user = auth()->user() )  {
                return ResponseService::error('Not authorization' );
            }
            if($order = $this->orderService->createFromRequest($request)){
                Notification::send($user, new OrderCreatedNotification( $order ));
                Notification::route('mail',  config('orders.orderManagerEmail', ""))->notify(new OrderCreatedAdminNotification( $order ));
            }

        }catch (\Exception $e){
            return ResponseService::error($e->getMessage());
        }

        return ResponseService::okMessageWithData('Order created',  ['orderId' => $order->id, 'payUrl' => $order->pay_url]);
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function show($id)
    {
        return view('orders::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {
        return view('orders::edit');
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function destroy($id)
    {
        //
    }

}
