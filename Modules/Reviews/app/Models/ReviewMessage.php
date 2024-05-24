<?php

namespace Modules\Reviews\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Content\Models\Content;
use Modules\Reviews\Database\Factories\ReviewMessageFactory;
use Modules\Reviews\Models\Review;

class ReviewMessage extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'review_id',
        'parent_id',
        'message',
        'author_id',
        'author'
    ];
    protected static function newFactory(): ReviewMessageFactory
    {
        return ReviewMessageFactory::new();
    }

    public function content(){
        return $this->morphMany(Content::class, 'contentable');
    }

    /**
     * Get the review that owns the message.
     */
    public function review()
    {
        return $this->belongsTo(Review::class);
    }
}
