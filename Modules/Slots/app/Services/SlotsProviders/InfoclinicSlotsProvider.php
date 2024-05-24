<?php

namespace Modules\Slots\Services\SlotsProviders;

use App\Services\Infoclinic\InfoclinicConnectService;

class InfoclinicSlotsProvider extends SlotsProviderAbstract
{

    public function createSlot():bool    {
        return true;
    }




    private InfoclinicConnectService $infoclinic;
    private array $doctorsIids;
    private array $clinicsIids;
    private int $dateFrom = 0;
    private int $dateTo = 0;
    private int $doctorType = 0;
    private int $onlyAutoReservedSlots = 0;
    private array $orderBy = [];


    public function __construct() {
        $this->infoclinic = new InfoclinicConnectService();
    }
    public function __destruct() {
        unset($this->infoclinic);
    }


}
