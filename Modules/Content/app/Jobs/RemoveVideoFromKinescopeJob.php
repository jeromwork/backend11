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

class RemoveVideoFromKinescopeJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected ?Content $contentKinescope = null;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Content $contentKinescope)
    {
        $this->contentKinescope = $contentKinescope;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if (!(new Kinescope())->delete($this->contentKinescope->file)) {//->file because there saved id kinescope
            //todo @telegram error
        }
        $this->contentKinescope->delete();
        $this->handleTargetModelCache($this->contentKinescope);


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
