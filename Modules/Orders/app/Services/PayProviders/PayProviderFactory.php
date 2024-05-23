<?php

namespace Modules\Orders\Services\PayProviders;
use Illuminate\Contracts\Container\Container;
class PayProviderFactory
{
    protected $container;

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    public function getService(string $serviceName)
    {
        $services = config("orders.payProviders", []);

        if (
            isset($services[$serviceName])
            && isset($services[$serviceName]["class"]) && class_exists($services[$serviceName]["class"])
        ) {
            return $this->container->make($services[$serviceName]["class"]);
        } else {
            return null;
        }
    }
}
