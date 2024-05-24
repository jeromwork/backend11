<?php

namespace Modules\Slots\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\ApiRequestQueryBuilders\ApiListService;
use App\Services\Response\ResponseService;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Notification;
use Modules\Doctors\Models\Doctor;
use Modules\Orders\Notifications\OrderCreatedAdminNotification;
use Modules\Orders\Notifications\OrderCreatedNotification;
use Modules\Orders\Services\OrderService;
use Modules\Slots\Services\SlotsProviders\SlotsProviderAbstract;

class SlotsController extends \Illuminate\Routing\Controller
{



    private ApiListService $QueryBuilderByRequest;
    private SlotsProviderAbstract $slotsProvider;
    private OrderService $orderService;
    public function __construct(
        ApiListService $apiHandler,
        SlotsProviderAbstract $slotsProvider,
        OrderService $orderService
    )    {
        $this->QueryBuilderByRequest = $apiHandler;
        $this->orderService = $orderService;
    }
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index()
    {
        return view('slots::index');
    }



    /**
     * Store a newly created slot in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(Request $request):JsonResponse
    {
        //during create slot, we create them in infoclinic

        //check free slot
        //check schedule
        //check doctor
        //check clinic
        //if mark online - create order
        //if order true, create slot in vendor
        //if created in vendor true create slot in db

        //notification about slot

        //run job autoCancel slot if not pay for 15 min
        // add slot info from order
        /**
         * Слот может быть без услуг - товаров
         * К примеру из инфоклиники
         * выглядит это так - пациент записался к доктору на время, но какие именно услуги неважно
         * если услуги важны, то создается ордер, с онлайн,наличко, pos оплатой
         * если в реквесте online = true, то обязательно создается ордер, и обязательно должны быть услуги (товары)
         */
        //


        try {
            if( !$user = auth()->user() )  {
                return ResponseService::error('Not authorization' );
            }





            if($order = $this->orderService->createFromRequest($request)){
                Notification::send($user, new OrderCreatedNotification( $order ));
                Notification::route('mail',  config('orders.orderManagerEmail', ""))->notify(new OrderCreatedAdminNotification( $order ));
            }
            if($request->input('doctor') && !$doctor = Doctor::where('id', $request->input('doctor'))->with('schedules')->first()){
                return ResponseService::error('Not have doctor');
            }



            return ResponseService::okMessageWithData('slots is created',  ['order_id' =>'n3109r2u3br', 'pay_url' => 'url'] );
        }catch (\Exception $e){
            return ResponseService::error($e->getMessage());
        }



    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function show($id)
    {
        return view('slots::show');
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
        //check type slot
    }

    public function getFreeSlots():?array    {
        return null;
    }

}
