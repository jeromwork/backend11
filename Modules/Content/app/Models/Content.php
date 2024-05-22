<?php

namespace Modules\Content\Entities;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Log;
use Ramsey\Uuid\Uuid;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Content extends Model
{
    use HasFactory;
    use HasUuids;

    protected $connection = DB_CONNECTION_DEFAULT;

    protected $fillable = [
        'file',
        'url',
//        'file_extension',
//        'file_name',
        'type',
        'typeFile',
        'confirm',
        'published',
        'targetClass',
        'contentable_type',
        'contentable_id',
        'parent_id',
        'mime',
        'is_preview_for',
        'original_file_name',
        'alt',
        'file_extension',
        'file_size',
        'left_handle_replicas',
    ];
    protected $table = 'contents';

    protected static function newFactory()
    {
        return \Modules\Content\Database\factories\ContentFactory::new();
    }


    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {

        static::deleted(function ($content) {
            Log::info('review content deleting!');
            //Storage::disk(self::STORAGE_DISK)->delete($content->file);
        });
    }

    /**
     * Generate a new UUID for the model.
     */
    public function newUniqueId(): string
    {
        return (string) Uuid::uuid4();
    }

    /**
     * Get the columns that should receive a unique identifier.
     *
     * @return array<int, string>
     */
    public function uniqueIds(): array
    {
        return ['id'];
    }


    public function contentable()
    {
        return $this->morphTo();
    }
    public function preview(): HasOne {
        return $this->hasOne(Content::class,'is_preview_for');
    }

    public function previewOriginal(): HasOne {
        return $this->hasOne(Content::class,'is_preview_for')->where('type', 'original');
    }

}
