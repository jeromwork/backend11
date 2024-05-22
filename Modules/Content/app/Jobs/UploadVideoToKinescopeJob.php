<?php

namespace Modules\Content\Jobs;

//use CURLFile;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;
use Modules\Content\app\Models\Content;
use Modules\Content\Services\ContentConverters\ContentConverterAbstract;
use Modules\Content\Services\ContentService;
use Modules\Content\Services\Kinescope;

class UploadVideoToKinescopeJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected ?string $contentId = null;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(string $contentId)
    {
        $this->contentId = $contentId;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $originalContent = Content::where('id', $this->contentId)->first();
        if (!$kinescopeInfo = (new Kinescope())->uploadContent($originalContent)) return ;


        Content::create([
            'file' => $kinescopeInfo['id'],
            'url' => $kinescopeInfo['play_link'],
            'type' => 'kinescope',
            'typeFile' => 'video',
            'mime' => 'hyperlink',
            'contentable_type'=> $originalContent->contentable_type,
            'contentable_id' => $originalContent->contentable_id,
            'is_preview_for' => '',
            'original_file_name' => $originalContent->original_file_name,
            'parent_id' => $originalContent->id,
            'targetClass' => $originalContent->targetClass,
            'confirm' => 1,
        ]);


        $this->handleTargetModelCache($originalContent);


    }


    protected function handleTargetModelCache( Content $originalContent):self{
        //run cache update for target class if exists
        if($originalContent->targetClass && method_exists($originalContent->targetClass, 'contentCacheUpdate')){
            if($target = $originalContent->targetClass::where('id', $originalContent->contentable_id)->first()){
                $target->contentCacheUpdate();
            }
        }
        return $this;
    }
}
