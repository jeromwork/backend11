<?php

namespace Modules\Health\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Health\Database\Factories\IserviceFactory;

class Iservice extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [];

    protected static function newFactory(): IserviceFactory
    {
        //return IserviceFactory::new();
    }
}
