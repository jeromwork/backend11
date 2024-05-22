<?php

namespace App\Services\Infoclinic;

use App\Models\User;
use Modules\Doctors\Entities\Doctor;

class SlotsService
{


    protected ?Doctor $doctor = null;
    protected ?User $user = null;
    protected ?int $patientId = null;
    protected ?int $clinicId = null;
    protected ?int $timeBegin = null;
//    protected ?Clinic $clinic = null;


    public function createSlot($doctorid, $date, $chairid, $bhour, $bminute, $ehour, $eminute, $cashid, $uid, $clientid, $fhour, $comment, $shedident) {


        $query = "SELECT SPRESULT FROM SCHEDADDREC(0, $doctorid, '$date', $chairid, 1, $bhour, $bminute, $ehour, $eminute, $cashid, $uid, $clientid, NULL, 1, NULL, '', 0, NULL, NULL, NULL, NULL, NULL, 0, $fhour, '$comment', 0, null, 15,  1, 1, NULL, NULL, NULL, $shedident, NULL, NULL, NULL, '')";
        global $modx;
        $modx->log(1, print_r($query, 1));
        return self::query($query);
    }







    public function toDoctor( Doctor $doctor ):self    {
        $this->doctor = $doctor;
        return $this;
    }

    public function forUser( User $user ):self    {
        $this->user = $user;
        return $this;
    }

    public function asPatientId( int $patientId):self{
        $this->patientId = $patientId;
        return $this;
    }

    public function toClinicId( int $clinicId):self{
        $this->clinicId = $clinicId;
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
