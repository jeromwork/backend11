<?php

namespace Modules\Profile\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Modules\Profile\Models\SmsVerification;

class ClearSmsVerificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $otpId;


    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($otpId)
    {
        $this->otpId = $otpId;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        SmsVerification::destroy($this->otpId);
    }
}
