<?php

namespace Modules\Schedules\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Schedules\Database\Factories\ScheduleFactory;

class Schedule extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */

    protected $connection = DB_CONNECTION_MODX;
    protected $table = 'modx_shd_work_time';
    protected $perPage = 10;
    protected $fillable = [
        'iid',
        'clinic_id',
        'clinic_iid',
        'doctor_id',
        'doctor_iid',
        'workplace_iid',
        'date',
        'ux_date',
        'work_begin',
        'work_end',
        'ux_work_begin',
        'ux_work_end',
        'slot_interval',
        'doc_type',
        'autoreserve_offset',
        'count_busy_slots',
        'cabinet_id',
        'cabinet_iid',

    ];

    protected static function newFactory(): ScheduleFactory
    {
        return ScheduleFactory::new();
    }
}
