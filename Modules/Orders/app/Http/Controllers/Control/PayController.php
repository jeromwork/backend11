<?php

namespace Modules\Orders\Http\Controllers\Control;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\Orders\Http\Requests\PayNotificationRequest;
use Modules\Orders\Services\PayProviders\Paykeeper;

class PayController extends \Illuminate\Routing\Controller
{


    public function notification(PayNotificationRequest $request)
    {
        //todo add $request use some pay providers

        $payProvider = new Paykeeper();
        return $payProvider->notificationFromPayProvider($request);
        return 'ok';
    }
}
