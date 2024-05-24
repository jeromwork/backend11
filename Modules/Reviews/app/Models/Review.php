<?php

namespace Modules\Reviews\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\Content\Models\Content;
use Modules\Reviews\Database\Factories\ReviewFactory;
use Modules\Reviews\Models\ReviewMessage;

class Review extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */

    protected $fillable = [
        'author',
        'author_id',
        'text',
        'contact',
        'reviewable_type',
        'reviewable_id',
        'rating',
        'published',
        'published_at',
        'is_new',
        'created_at',
        'after_course',
        'source',



    ];

    protected static function newFactory():ReviewFactory
    {
        return ReviewFactory::new();
    }

    protected $perPage = 10;


    // Define the date columns
//    protected $dates = ['published_at'];
//
//    // Define mutator for published_at attribute
//    public function setPublishedAtAttribute($value)
//    {
//        $this->attributes['published_at'] = strtotime($value);
//    }


    public function reviewable()
    {
        return $this->morphTo();
    }

    public function content(){
//        return $this->hasMany(ReviewContent::class);

        return $this->morphMany(Content::class, 'contentable');
    }


    public function messages():HasMany{
        return $this->hasMany(ReviewMessage::class);
    }

    public function getMorphClass()
    {
        return 'review';
    }


// this is a recommended way to declare event handlers
    public static function boot() {
        parent::boot();

//        static::deleting(function($review) {
//            $content = $review->content();
//            $content = ReviewContent::where('')->content();
//            if($content){
//                foreach ($content as $item) $item->delete();
//            }
//            Log::inreview_messagesfo('review deleting!');
//            // do the rest of the cleanup...
//        });
    }

}
