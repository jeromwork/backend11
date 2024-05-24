<?php


namespace Modules\Reviews\Services;
use Modules\Reviews\Models\Review;
use Nwidart\Modules\Facades\Module;


class ReviewService
{


    public function delete(int $id):bool {
        $review = Review::find($id);
        if(!$review) return  false;

        //remove bind messages
        //$review->message()->delete();
        if($review->delete()){
            return true;
        }
        return true;
    }

}
