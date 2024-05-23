<?php

namespace Modules\Health\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Modules\Health\Database\Factories\SeoFactory;

class Seo extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = ['name', 'title', 'url', 'active', 'type'];
    protected $table = 'health_seo';

    protected static function newFactory()
    {
        return \Modules\Health\Database\factories\SeoFactory::new();
    }



    public function variations(): BelongsToMany
    {
        return $this->belongsToMany(Variation::class, 'health_seo_variation');
    }
}
