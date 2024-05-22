<?php

namespace Modules\Content\Services;
use Illuminate\Support\Facades\Log;
use YouTube\DownloadOptions;
use FFMpeg\FFMpeg;
use FFMpeg\Format\Video\X264;
class YoutubeDownloadOptionsService extends DownloadOptions
{
    /**
     * Find fullHD YouTube download link
     * @return array
     */
    public function getFullHdDownloadLink(): array
    {
        $url = '';
        $bitrate = 0;
        $audioUrl = '';
//        $audioBitrate = 0;
//        $audio = false;
        $quality = ['hd1080'=>'hd1080', 'hd720' => 'hd720','tiny' => 'tiny', 'small' => 'small'];
        $formats = parent::getAllFormats();
        foreach ($formats as $item) {
            if ($item->quality == $quality['hd720'] && $item->bitrate > $bitrate && $item->audioQuality) {
                $url = $item->url;
                $bitrate = $item->bitrate;
//                Log::info('audio ' . (boolval($item->audioQuality)) ? 'true' : 'false');
//                if(!$item->audioQuality || $item->audioQuality === ' ') {
//                    $audio = true;
//                }
            }
//            if(strpos($item->mimeType, 'audio/mp4') && $item->bitrate > $audioBitrate && $audio) {
//                $audioBitrate = $item->bitrate;
//                $audioUrl = $item->url;
//            }
        }

        return ['video' => $url, 'audio' => $audioUrl];
    }
}
