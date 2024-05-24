<?php

namespace Modules\Orders\Services\PayProviders;

use App\Models\User;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Modules\Orders\DataStructures\CartStructure;
use Modules\Orders\DataStructures\ClientStructure;
use Modules\Orders\DataStructures\InvoiceStructure;
use Modules\Orders\Models\Order;
use Carbon\Carbon;
use Modules\Orders\Http\Requests\Control\PayNotificationRequest;

class Paykeeper implements PayProviderInterface
{

    protected string $server;
    protected string $login;
    protected string $password;
    protected string $secretKey;

    protected ?string $token = null;

    protected ?Order $order = null;
    protected ?User $user = null;
    protected ?CartStructure $cart = null;


    public function __construct()    {
        $this->server = config('orders.payProviders.paykeeper.server', "");
        $this->login = config('orders.payProviders.paykeeper.login', "");
        $this->password = config('orders.payProviders.paykeeper.password', "");
        $this->secretKey = config('orders.payProviders.paykeeper.secretKey', "");

        if( !$this->server || !$this->login || !$this->password || !$this->secretKey) throw new \Exception('Not set settings paykeeper in app/config/orders');

    }
    protected function getHeaders():array{
        $base64 = base64_encode("$this->login:$this->password");
        return [
//            'Content-Type' => 'application/x-www-form-urlencoded',
//            'Authorization' => 'Basic '.$base64,
            'Content-Type: application/x-www-form-urlencoded',
            'Authorization: Basic '.$base64
        ];
    }


    public function createInvoice():?InvoiceStructure  {
        if(!$this->order || !$this->cart || !$this->user) throw new \Exception('Not set neccesaraly data for work paykeeper');
        $data = [
            'pay_amount' => $this->cart->sum,
            'clientid' => $this->user->fio,
            'orderid' => $this->order->id,
            'service_name' =>$this->cart->goods->map(fn ($item) => $item->name)->implode(', '),
            'client_email' => $this->user->email,
            'client_phone' => $this->user->phone,
            //'expiry' => Carbon::now()->addHours(1),
            'token' => $this->getToken(),
            ];


        $invoiceData = $this->responseData(
            Http::asForm()->withBasicAuth($this->login,$this->password)
                ->withOptions([
                    'verify' => true, // CURLOPT_SSL_VERIFYPEER
                    'verify_host' => 2, // CURLOPT_SSL_VERIFYHOST
                    'header' => false, // CURLOPT_HEADER
                ])
                ->post( $this->server.'/change/invoice/preview/',$data));
        if ($invoiceData && isset($invoiceData['invoice_id']) && isset($invoiceData['invoice_url']) && isset($invoiceData['invoice'])) return new InvoiceStructure([
            'id' => $invoiceData['invoice_id'],
            'url' => $invoiceData['invoice_url'],
            'text' => $invoiceData['invoice_url'],
            ]);
        else return null;
    }

    public function forOrder(Order $order):self    {
        $this->order = $order;
        return $this;
    }

    public function forCart(CartStructure $cartStructure):self    {
        $this->cart = $cartStructure;
        return $this;
    }

    public function forUser(User $user):self    {
        $this->user = $user;
        return $this;
    }

    protected function getToken():?string{
        if($this->token) return $this->token;
        $response = $this->responseData(
            Http::withBasicAuth($this->login,$this->password)
//                ->withOptions([
//                    'verify' => true, // CURLOPT_SSL_VERIFYPEER
//                    'verify_host' => 2, // CURLOPT_SSL_VERIFYHOST
//                    'header' => false, // CURLOPT_HEADER
//                ])
                ->get($this->server.'/info/settings/token')
        );
        if(!isset($response['token'])) throw new \Exception('Not have token');
        return $this->token =  $response['token'];
    }

    protected function responseData( $response):?array{
        $status = $response->status(); // Get the HTTP status code
        $data = null;
        if ($response->successful()) {
            // Request was successful (status code 2xx)
//            $body = $response->json();
            $data = $response->json(); // Get JSON response
            // Handle the response data
        } else {
            //todo @telegram errors
            // Request failed
            $status = $response->status(); // Get the HTTP status code
            $error = $response->body(); // Get the response body
            // Handle the error
            //todo @telegram errors
            return null;
        }

        return $data;
    }

//    protected function Http():PendingRequest{
//        return Http::withBasicAuth($this->login,$this->password)
//            ->withOptions([
//                'verify' => true, // CURLOPT_SSL_VERIFYPEER
//                'verify_host' => 2, // CURLOPT_SSL_VERIFYHOST
//                'header' => false, // CURLOPT_HEADER
//            ])->baseUrl($this->server);

    public function notificationFromPayProvider(PayNotificationRequest $request):?string    {

        Log::channel('payment')->info('Paykeeper callback data '.json_encode($request->all()));
        if(!$order = Order::where('id', $request->input('orderid'))->first()){
            Log::channel('payment')->info('Not have order by id  '. $request->input('orderid'));
            //todo @telegram errors
            return null;
        }
        $order->update(['status' => 'paid']);

        return "OK ".md5($request->input('id').$this->secretKey);
    }

}
