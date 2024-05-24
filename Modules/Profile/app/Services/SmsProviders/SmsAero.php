<?php

namespace Modules\Profile\Services\SmsProviders;

use SmsAero\SmsAeroMessage;

class SmsAero implements SmsProviderInterface
{
    private $baseUrl = 'https://gate.smsaero.ru/v2/';

    protected string $phone = '';
    protected string $sub = '';
    /**
     * API key given by smsAero
     *
     * @var string
     */
    private $apiKey;
    private $sign;
    private $login;

    public function __construct()    {
        if(!$this->apiKey = config('profile.smsProviders.smsAero.apiKey', "")) {
            throw new \Exception('Not set settings sms aero');
        }
        if(!$this->sign = config('profile.smsProviders.smsAero.sign', "SMS Aero")){
            throw new \Exception('Not set settings sms aero');
        }
        if(!$this->login = config('profile.smsProviders.smsAero.login', "")){
            throw new \Exception('Not set settings sms aero');
        }

    }

    public function toPhone( string $phone ):self   {
        $this->phone = $phone;
        return $this;
    }

    public function from( string $sub ):self    {
        $this->sub = $sub;
        return $this;
    }
    public function sendSms( string $msg ):bool{
         $response = $this->send(['number' => $this->phone, 'text' => $msg, 'sign'=>$this->sign]);
        if($response && $response['success']) return true;
        return false;
    }




    public function send($params)
    {
        return $this->makeRequest('POST', 'sms/send', $params);
    }

    /**
     * Выполняет запрос к API-интерфейсу SMS Aero.
     *
     * @param string $method HTTP метод, который должен быть использован при запросе.
     * @param string $url Путь запроса относительно базового URL API.
     * @param array $params Массив параметров запроса.
     *
     * @return array Ответ API.
     * @throws \Exception
     */
    public function makeRequest($method, $url, $params = [])
    {
        if (!extension_loaded('curl')) {
            throw new \Exception('Расширение curl не установлено');
        }

        $curl = curl_init();

        // Устанавливаем параметры для запроса.
        $url = $this->baseUrl . $url;

        if ($method == 'GET' && !empty($params)) {
            $url .= '?' . http_build_query($params);
        }

        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_HTTPHEADER, [
            'Authorization: Basic ' . base64_encode($this->login . ':' . $this->apiKey)
        ]);

        if ($method == 'POST' && !empty($params)) {
            curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($params));
        }

        // Выполняем запрос.
        $result = curl_exec($curl);

        // Проверяем наличие ошибок.
        if ($error = curl_error($curl)) {
            throw new \Exception('Ошибка выполнения запроса: ' . $error);
        }

        // Закрываем соединение.
        curl_close($curl);

        return json_decode($result, true);
    }
}
