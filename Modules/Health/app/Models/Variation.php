<?php

namespace Modules\Health\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Doctors\Models\Doctor;
use Modules\Health\Database\Factories\VariationFactory;

class Variation extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = ['name', 'price', 'active'];
    protected $table = 'health_variations';
    public const RELATIONS_METHODS = [Doctor::class=> 'doctors'];
    public const MODEL_RELATION_ALIAS = 'variations';

    protected static function newFactory()
    {
        return \Modules\Health\Database\factories\VariationFactory::new();
    }

    public function iservice()
    {
        return $this->belongsTo(Iservice::class);
    }

    public function doctors()
    {
        return $this->belongsToMany(Doctor::class, 'health_doctor_variation')->withPivot(['custom_price', 'use_always', 'active']);
    }

    public function seo()
    {
        return $this->belongsToMany(Seo::class, 'health_seo_variation');
    }
}
