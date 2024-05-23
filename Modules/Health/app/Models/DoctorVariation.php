<?php

namespace Modules\Health\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Health\Database\Factories\DoctorVariationFactory;

class DoctorVariation extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [];

    protected static function newFactory(): DoctorVariationFactory
    {
        //return DoctorVariationFactory::new();
    }
}
