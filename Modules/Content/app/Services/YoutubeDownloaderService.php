<?php

namespace Modules\Content\Services;
use YouTube\Exception\TooManyRequestsException;
use YouTube\Exception\VideoNotFoundException;
use YouTube\Exception\YouTubeException;
use YouTube\Models\VideoInfo;
use YouTube\Models\YouTubeCaption;
use YouTube\Models\YouTubeConfigData;
use YouTube\Responses\PlayerApiResponse;
use YouTube\Responses\VideoPlayerJs;
use YouTube\Responses\WatchVideoPage;
use YouTube\Utils\Utils;
use YouTube\YouTubeDownloader;
use YouTube\SignatureLinkParser;
use YouTube\VideoInfoMapper;
use Modules\Content\Services\YoutubeDownloadOptionsService;

class YoutubeDownloaderService extends YouTubeDownloader
{


    public function getDownloadLinks(string $video_id, array $extra = []): YoutubeDownloadOptionsService
    {
        $video_id = Utils::extractVideoId($video_id);

        if (!$video_id) {
            throw new \InvalidArgumentException("Invalid Video ID: " . $video_id);
        }

        $page = parent::getPage($video_id);

        if ($page->isTooManyRequests()) {
            throw new TooManyRequestsException($page);
        } elseif (!$page->isStatusOkay()) {
            throw new YouTubeException('Page failed to load. HTTP error: ' . $page->getResponse()->error);
        } elseif ($page->isVideoNotFound()) {
            throw new VideoNotFoundException();
        } elseif ($page->getPlayerResponse()->getPlayabilityStatusReason()) {
            throw new YouTubeException($page->getPlayerResponse()->getPlayabilityStatusReason());
        }

        // a giant JSON object holding useful data
        $youtube_config_data = $page->getYouTubeConfigData();

        // the most reliable way of fetching all download links no matter what
        // query: /youtubei/v1/player for some additional data
        $player_response = $this->getPlayerApiResponse($video_id, $youtube_config_data);

        // get player.js location that holds URL signature decipher function
        $player_url = $page->getPlayerScriptUrl();
        $response = $this->getBrowser()->get($player_url);
        $player = new VideoPlayerJs($response);

        $links = SignatureLinkParser::parseLinks($player_response, $player);

        // since we already have that information anyways...
        $info = VideoInfoMapper::fromInitialPlayerResponse($page->getPlayerResponse());

        return new YoutubeDownloadOptionsService($links, $info);
    }
}
