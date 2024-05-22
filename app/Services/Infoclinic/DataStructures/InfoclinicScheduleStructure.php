<?php


namespace App\Services\Infoclinic\DataStructures;

class InfoclinicScheduleStructure extends \App\DataStructures\AbstractDataStructure
{

    public ?int $id = null;
    public ?int $doctorId = null;
    public ?int $cabinetId = null;
    public ?int $clinicId = null;

    public ?int $interval = null;
    public ?int $workBegin = null;
    public ?int $workEnd = null;



}
