<?php

namespace Modules\Doctors\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\Content\Entities\Content;
use Modules\Content\Services\ContentLegacyCacheService;
use Modules\Doctors\Database\Factories\DoctorFactory;
use Modules\Doctors\Entities\DoctorDiplom;
use Modules\Health\Entities\DoctorVariation;
use Modules\Health\Entities\Variation;
use Modules\Schedules\Entities\Schedule;

class Doctor extends Model
{
    use HasFactory;

    protected $connection = DB_CONNECTION_MODX;

    protected $table = 'modx_doc_doctors';
    //todo permission
    protected $fillable = ['surname', 'name', 'middlename', 'diploms_cache', 'content_cache' ];

    protected $perPage = 10;
//    protected static function newFactory()
//    {
//        return \Modules\Doctors\Database\factories\DoctorFactory::new();
//    }


    protected static function newFactory()
    {
        return DoctorFactory::new();
    }

    public function contentRaw(){
        return $this->morphMany(Content::class, 'contentable');
    }

    public function content(){
        return $this->contentRaw()
            ->with('preview')
            ->where('confirm', 1)
            ->where('is_preview_for', '')
            ->where('type', '!=', 'original')
            ->where('type', '!=', 'kinescope');
    }

    public function contentOriginal(){
        return $this->contentRaw()
            ->with('previewOriginal')
            ->where('confirm', 1)
            ->where('is_preview_for', '')
            ->where('type', 'original');
    }


    public function diploms():HasMany   {
        return $this->hasMany(DoctorDiplom::class)->with('content');
    }
    public function diplomsOriginal():HasMany   {
        return $this->hasMany(DoctorDiplom::class)->with('contentOriginal');
    }

    public function schedules():HasMany   {
        return $this->hasMany(Schedule::class);
    }


    public function getMorphClass()
    {
        return 'doctor';
    }

    /**
     * Update content_cache column in modx db
     * @return $this
     */

    public function contentCacheUpdate():self    {
        $contentCache = [];
        //always load content relation with order by updated time
        $this->load([ 'content' => function ($query) {
            $query->orderBy('updated_at');
        }, 'diploms' => function ($query) {
            $query->orderBy('updated_at');
        }]);

        $this->content_cache = json_encode((new ContentLegacyCacheService())->forContent($this->content)->getLegacyContentData());
        $diploms = [];
        if($this->diploms){
//            $diploms = $this->diploms->toArray();
            foreach ($this->diploms as $diplom){
                if($diplom->content){
                    $diploms[$diplom->id] = $diplom->setVisible(['id', 'title', 'published'])->toArray();
                    $diploms[$diplom->id]['content'] = (new ContentLegacyCacheService())->forContent($diplom->content)->getLegacyContentData();

                }
            }
        }
        $this->diploms_cache = json_encode(array_values($diploms));


        $this->save();
        return $this;
    }


    public function variations(): BelongsToMany
    {
        return $this->setConnection('localhost')->belongsToMany(
            Variation::class, // Adjust the namespace and model name as per your actual Variation model
            'health_doctor_variation', // Pivot table name
            'doctor_id', // Foreign key on the pivot table that references the Doctor model
            'variation_id' // Foreign key on the pivot table that references the Variation model
        )->using(DoctorVariation::class) // Optional: if you have a custom pivot model
        ->withPivot(['price', 'use_always', 'active']) ; // Pivot table additional columns

    }



}
