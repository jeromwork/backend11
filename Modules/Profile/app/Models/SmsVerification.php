<?php

namespace Modules\Profile\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Profile\Database\Factories\SmsVerificationFactory;

class SmsVerification extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = ['phone', 'code', 'expiration', 'try'];

    protected static function newFactory(): SmsVerificationFactory
    {
        return SmsVerificationFactory::new();
    }
}
