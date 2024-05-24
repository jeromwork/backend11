<?php

namespace Modules\Profile\Services\SmsProviders;

interface SmsProviderInterface
{

    public function toPhone( string $phone):self;
    /**
     * Sends the OTP to the user with optionally using the reference number
     *
     * @param string $phone  : Phone number
     * @param string $otp : The One-Time-Password
     * @return boolean
     */
    public function sendSms( string $msg );
}
