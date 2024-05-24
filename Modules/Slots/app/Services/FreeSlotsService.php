<?php

namespace Modules\Slots\Services;

use Illuminate\Support\Collection;
use Modules\Doctors\Models\Doctor;

class FreeSlotsService
{

    protected ?Collection $doctors = null;
    protected int $dateFrom = 0;
    protected int $dateTo = 0;

    public function getSlots(array $clinicDoctorDays = []):array{
        $DoctorDaySlots = [];

        $k_a_busy_slots = [];
        $busySlotsByKeys = [];
        foreach ($this->getBusySlots($clinicDoctorDays) as $busy_slot){
            $k_a_busy_slots[$busy_slot['clinic_iid']][$busy_slot['doctor_iid']][$busy_slot['ux_date']][] = $busy_slot;
            $busySlotsByKeys[$busy_slot['clinic_iid']][$busy_slot['doctor_id']][$busy_slot['ux_date']][] = $busy_slot;
        }

        $eastSchedule = new \Schedule\Schedules();
        $schedules = [];
        if($clinicDoctorDays){
            $schedules = $eastSchedule->get($clinicDoctorDays);
        }elseif($this->doctorsIids && $this->dateFrom && $this->dateTo){
            $schedules = $eastSchedule->whereDoctorsIids($this->doctorsIids)->whereDays($this->dateFrom, $this->dateTo)->get();
        }
        else{
            throw new \Exception('Не настроен объект eastSlots для выдачи слотов', 591);
        }

        foreach ($schedules as $schedule){

            $a_day_busy_slots = ( isset($k_a_busy_slots[$schedule['clinic_iid']][$schedule['doctor_iid']][$schedule['ux_date']]) ) ? $k_a_busy_slots[$schedule['clinic_iid']][$schedule['doctor_iid']][$schedule['ux_date']]: [] ;
            $busySlotsInDay = ( isset($busySlotsByKeys[$schedule['clinic_iid']][$schedule['doctor_id']][$schedule['ux_date']]) ) ? $busySlotsByKeys[$schedule['clinic_iid']][$schedule['doctor_id']][$schedule['ux_date']]: [] ;
            $a_debug_data = [];
            if($this->debug_time_to_str){

                $a_debug_data = [
//                    '_date' => date('d.m.Y', $schedule['ux_date']),
//                    '_work_begin' => date('d.m.Y H:i', $schedule['ux_work_begin']),
//                    '_work_end' => date('d.m.Y H:i', $schedule['ux_work_end']),
                ];
            }
            $key = $schedule['clinic_iid'].'_'.$schedule['doctor_id'].'_'.$schedule['ux_date'];
//            $key = $schedule['clinic_iid'].$schedule['doctor_iid'].$schedule['ux_date'];
            $DoctorDaySlots[$key] =
                [
                    'clinic_iid' => $schedule['clinic_iid'],
                    'doctor_iid' => $schedule['doctor_iid'],
                    'doctor_id' => $schedule['doctor_id'],
                    'date' => $schedule['ux_date'],
                    'slot_interval' => $schedule['slot_interval'],
                    'work_begin' => $schedule['ux_work_begin'],
                    'work_end' => $schedule['ux_work_end'],
                    'slots' =>
                        array_unique(array_merge(
                            ( isset($DoctorDaySlots[$key]['slots']) && $DoctorDaySlots[$key]['slots']) ? $DoctorDaySlots[$key]['slots'] : [],
                            $this->_get_free_slots( $schedule['ux_work_begin'], $schedule['ux_work_end'], $schedule['slot_interval'], $busySlotsInDay)
                        ))


                ];

        }

        return array_values($DoctorDaySlots);
    }



}
