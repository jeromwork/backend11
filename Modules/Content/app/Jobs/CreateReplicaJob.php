<?php

namespace Modules\Content\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Modules\Content\Services\ContentConverters\app\Services\ContentConverterAbstract;

class CreateReplicaJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected ContentConverterAbstract $previewService;

    /**
     * CreatePreviewJob constructor.
     * @param ContentConverterAbstract $previewService
     */
    public function __construct(ContentConverterAbstract $previewService)
    {
        $this->previewService = $previewService;

    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->previewService->generateReplicas();
        return;
    }

}
