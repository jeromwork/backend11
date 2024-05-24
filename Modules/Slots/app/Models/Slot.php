<?php

namespace Modules\Slots\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Slots\Database\Factories\SlotFactory;

class Slot extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $connection = DB_CONNECTION_MODX;

    protected $table = 'modx_eslot_slots';

    //use this fiels, when move slots table in eastlar db

    protected $fillable = [
        'iid', 'ux_begin', 'ux_end', 'ux_date', 'time_from', 'time_to','date',
        'doctor_id', 'doctor_iid', 'clinic_id', 'clinic_iid', 'cabinet_id',
        'patient_id', 'slot_type', 'doc_type',
        'is_autoreserve', 'slot_create_time', 'vendor_id', 'order_id', 'note',
    ];


    protected static function newFactory(): SlotFactory
    {
        return SlotFactory::new();
    }
}
