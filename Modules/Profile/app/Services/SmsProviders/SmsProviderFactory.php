<?php

namespace Modules\Profile\Services\SmsProviders;

class SmsProviderFactory
{
    public function getService( string $serviceName)
    {
        $services = config("profile.smsProviders", []);
        if (
            isset($services[$serviceName])
            && isset($services[$serviceName]["class"]) && class_exists($services[$serviceName]["class"])
        ) {
            return new $services[$serviceName]["class"]();
        } else {
            return null;
        }
    }
}
