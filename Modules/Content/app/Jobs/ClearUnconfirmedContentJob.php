<?php

namespace Modules\Content\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Modules\Content\app\Models\Content;
use Modules\Content\Services\ContentService;


class ClearUnconfirmedContentJob implements ShouldQueue
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
        $originalContent = Content::where('id', $this->contentId)->where('confirm', 0)->first();
        if(!$originalContent || !$originalContent->id ) return ;
        $contentService = new ContentService();
        $fileOriginalFullPath = $contentService->getOriginalDisk()->path($originalContent->file);
        if( file_exists($fileOriginalFullPath) ) {
            unlink($fileOriginalFullPath);
        }
        $originalContent->delete();
        //todo if folder is empty, remove folder

    }
}
