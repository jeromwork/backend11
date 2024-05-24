<?php

namespace Modules\Slots\Services\SlotsProviders;

use App\Models\User;
use Modules\Doctors\Entities\Doctor;

abstract class  SlotsProviderAbstract
{

    protected ?Doctor $doctor = null;
    protected ?User $user = null;
    protected ?int $timeBegin = null;
//    protected ?Clinic $clinic = null;
    public abstract function createSlot():bool;

    public function toDoctor( Doctor $doctor ):self    {
        $this->doctor = $doctor;
        return $this;
    }

    public function forUser( User $user ):self    {
        $this->user = $user;
        return $this;
    }

//    public function inClinic( Clinic $clinic ):self    {
//        $this->clinic = $clinic;
//        return $this;
//    }

    public function inTime( int $timeBegin, int $timeEnd = 0):self    {
        //todo check schedule and duration
        $this->timeBegin = $timeBegin;
        return $this;
    }

}
