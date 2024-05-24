<?php

namespace Modules\Profile\Services;

use App\Services\Response\ResponseService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use Modules\Profile\Entities\SmsVerification;
use Modules\Profile\Jobs\ClearSmsVerificationJob;
use Modules\Profile\Services\SmsProviders\SmsProviderFactory;
use function Symfony\Component\Translation\t;

class SmsVerificationService
{
    protected string $phone;
    protected ?Model $smsInfo = null;
    public function __construct(string $phone)    {
        $this->phone = $phone;
        $this->smsInfo = SmsVerification::where('phone', Hash::make($phone))->first();
    }


    public function checkCode(string $code):bool    {
        $smsInfo = SmsVerification::where('phone', hash('sha256', $this->phone),)->where('code', hash('sha256', $code))->first();
        if ($smsInfo){
            //here bad man can spend sms ballance
            $smsInfo->delete();
            return true;
        }
        return  false;
    }

    public function sendSms()
    {
        if (!config("profile.smsServiceEnabled", false)) return false;
        if(!$this->checkPossibleSendSms()) return null;

        $this->discardOldCode();
        $tempCode = $this->createTempCode();
        if (!empty($tempCode)) {
            if($this->sendSmsByProvider( $tempCode )){

                if($this->smsInfo && $this->smsInfo->id){
                    $this->smsInfo->update([
                        'code' => hash('sha256', $tempCode),
                        'expiration' => time() + config("profile.smsTimeout", 180),
                        'try' => $this->smsInfo->try++
                    ]);
                }else{
                    //save verification data
                    $smsInfo = SmsVerification::create([
                        'phone' => hash('sha256', $this->phone),
                        'code' => hash('sha256', $tempCode),
                        'expiration' => time() + config("profile.smsTimeout", 180),
                        'try' => 1
                    ]);

                    ClearSmsVerificationJob::dispatch($smsInfo->id)->delay(config("profile.timeoutBetweenEntrance", 24*3600));
                }
            }
            return true;
        }

        return false;
    }

    protected function checkPossibleSendSms():bool{

        if( !$this->smsInfo ) return true;
        if ($this->smsInfo->try >= config("profile.smsAttempts", 3)){
            throw new \Exception('Try to login later');
        }
        if ($this->smsInfo->expiration <= time()){
            throw new \Exception('Wait sms');
        }
        return true;
    }

    private function sendSmsByProvider( int $tempCode ):bool    {
        if(!$smsProviderName = config("profile.smsDefaultProvider", null)) return false;
        $smsProvider = (new SmsProviderFactory())->getService($smsProviderName);
        if ($smsProvider) {
            return $smsProvider->toPhone($this->phone)->sendSms( $tempCode );
        }
        return false;
    }

    public function getSmsTimeout():int    {
        return config("profile.smsTimeout", 120);
    }


    public function createTempCode():string {
        $digits = config("profile.tempCodeLength", 4);
        $number = strval(rand((int) pow(10, $digits - 1), (int) pow(10, $digits) - 1));
        return substr($number, 0, $digits);
    }

    public function discardOldCode():bool    {
        if($this->smsInfo) $this->smsInfo->update(['expiration' => 0, 'try' => 0, 'code' => 0]);
        return true;
    }



    public function isExpired()
    {
        return $this->created_at < Carbon::now()->subSeconds(config("otp.otp_timeout", 300));
    }
}
