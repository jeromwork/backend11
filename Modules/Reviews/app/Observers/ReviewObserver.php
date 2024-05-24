<?php

namespace Modules\Reviews\Observers;

use Modules\Reviews\Models\Review;

class ReviewObserver
{
    /**
     * Handle the ReviewObserver "created" event.
     */
    public function created(Review $review): void
    {
        //
    }

    /**
     * Handle the ReviewObserver "updated" event.
     */
    public function updated(Review $review): void
    {
        //
    }

    /**
     * Handle the ReviewObserver "deleted" event.
     */
    public function deleted(Review $review): void
    {
        //
    }

    /**
     * Handle the ReviewObserver "restored" event.
     */
    public function restored(Review $review): void
    {
        //
    }

    /**
     * Handle the ReviewObserver "force deleted" event.
     */
    public function forceDeleted(Review $review): void
    {
        //
    }

    public function deleting(Review $review): void
    {
        //clear attach files
        if($reviewContent = $review->content){
            foreach ($reviewContent as $content){
                //(new ReviewContentService())->removeContent($content);
            }
        }
    }
}
