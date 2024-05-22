<?php

namespace App\Services\Infoclinic;

use App\Models\User;
use App\Services\Infoclinic\DataStructures\InfoclinicScheduleStructure;
use Modules\Doctors\Entities\Doctor;

class SchedulesService
{


    protected ?Doctor $doctor = null;
    protected ?User $user = null;
    protected ?int $doctorId = null;
    protected ?int $patientId = null;
    protected ?int $clinicId = null;
    protected ?int $timeBegin = null;
//    protected ?Clinic $clinic = null;


    protected InfoclinicConnectService $infoclinic;

    public function __construct()    {
        $this->infoclinic = new InfoclinicConnectService();
    }

    public function getSchedule() {

        if(!$this->timeBegin) throw new \Exception('not set time begin slot');
        if(!$this->doctorId) throw new \Exception('not set doctor info');
        $workdate = date('d.m.Y', $this->timeBegin);
        $query = "SELECT CASHID,CHAIR,SCHEDIDENT, BEGHOUR, BEGMIN, ENDHOUR, ENDMIN, SHINTERV, DCODE, WDATE FROM DOCTSHEDULE WHERE WDATE = '$workdate'  AND DCODE=$this->doctorId;";
        if(!$scheduleInfo = $this->infoclinic->query($query)) return null;

        return $this->convertToSchedule($scheduleInfo);
    }



    protected function convertToSchedule(array $scheduleFromInfoclinic ):InfoclinicScheduleStructure{
        return new InfoclinicScheduleStructure([
           'id' =>  $scheduleFromInfoclinic['SCHEDIDENT'],
           'doctorId' =>  $scheduleFromInfoclinic['DCODE'],
           'cabinetId' =>  $scheduleFromInfoclinic['CHAIR'],
           'clinicId' =>  $scheduleFromInfoclinic['CASHID'],
           'interval' =>  $scheduleFromInfoclinic['SHINTERV'] * 60,
            'workBegin' =>  $scheduleFromInfoclinic['SHINTERV'],
            'workEnd' =>  $scheduleFromInfoclinic['SHINTERV'],
        ]);
    }



    public function toDoctor( Doctor $doctor ):self    {
        $this->doctor = $doctor;
        return $this;
    }

    public function forDoctorId( int $doctorId ):self    {
        $this->doctorId = $doctorId;
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
