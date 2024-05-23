<?php

namespace Modules\Doctors\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\Content\App\Models\Content;


class DoctorDiplom extends Model
{
    use HasFactory;
    protected $connection = DB_CONNECTION_DEFAULT;
    protected $fillable = ['title', 'doctor_id', 'published'];

//    protected $table = 'doctor_diploms';

    protected $perPage = 10;

    protected static function newFactory()
    {
        return \Modules\Doctors\Database\factories\DoctorDiplomFactory::new();
    }

    public function contentRaw(){
        return $this->morphMany(Content::class, 'contentable');
    }

    public function content(){
        return $this->contentRaw()
            ->with('preview')
            ->where('confirm', 1)
            ->where('is_preview_for', '')
            ->where('type', '!=', 'original');
    }

    public function contentOriginal(){
        return $this->contentRaw()
            ->with('preview')
            ->where('confirm', 1)
            ->where('is_preview_for', '')
            ->where('type', 'original');
    }

    public function getMorphClass()
    {
        return 'doctorDiplom';
    }

    /**
     * Get the review that owns the message.
     */
    public function doctor()
    {
        return $this->belongsTo(Doctor::class);
    }

    public function contentCacheUpdate():self    {
        $this->load([ 'doctor']);
        $this->doctor->contentCacheUpdate();

        return $this;
    }


}
