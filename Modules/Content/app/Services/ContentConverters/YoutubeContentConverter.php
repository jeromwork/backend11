<?php

namespace Modules\Content\Services\ContentConverters;

use Illuminate\Support\Facades\Log;
use Modules\Content\app\Models\Content;
use Modules\Content\Jobs\CreateReplicaJob;
use Modules\Content\Services\ContentService;
use Modules\Content\Services\YoutubeDownloaderService;
use YouTube\Exception\YouTubeException;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Exception\GuzzleException;
use App\DataStructures\Content\CreateReplicaContentStructure;

class YoutubeContentConverter extends ContentConverterAbstract
{

    public function generateReplicas():bool {
        if( !$this->originalContentId || !$this->key)       return false;
        $originalContent = Content::where('id', $this->originalContentId)->first();
        if(!$originalContent || !$originalContent->id ) return false;
        if($originalContent->typeFile !== 'videoLinkYoutube') {
            return false;
        }
        $downloadLink = 'https://www.youtube.com/watch?v=' . $originalContent->original_file_name;
        $link = [];
        $info = null;
        //todo possible extension from youtube can be not equal mp4
        $extension = 'mp4';
        $youtubeService = new YouTubeDownloaderService();

        try {
            $downloadOptions = $youtubeService->getDownloadLinks($downloadLink);
            if ($downloadOptions->getAllFormats()) {
                $link = $downloadOptions->getFullHdDownloadLink();
                $info = $downloadOptions->getInfo();
            } else {
                throw new YouTubeException('No download links');
            }
        } catch (YouTubeException $e) {
            echo 'Something went wrong: ' . $e->getMessage();
        }

        $disk = (new ContentService())->getStorageDisk();

       $fileFolderMD5 =  md5(date('Y-m-d'));

        if (!$disk->exists($fileFolderMD5)) {
            // If the folder in disk doesn't exist, create it
            $disk->makeDirectory($fileFolderMD5);
        }
        $replicaFilename = uniqid().'.'.$extension;
        $replicaFilePath = $fileFolderMD5.DIRECTORY_SEPARATOR.$replicaFilename;
        try {
            // Send GET-request for file download

            $response = (new GuzzleClient([
                'verify' => false // Disable ssl
            ]))->get($link['video'] ?? '', ['sink' => $disk->path($replicaFilePath)]);

            // check status code response from youtube
            if ($response->getStatusCode() !== 200) {
                //todo send error report to telegram
                Log::error("Произошла ошибка при скачивании файла");
                return false;
            }

            //check content data through data structure
            $replicaFileInfo = new CreateReplicaContentStructure(
                [
                    'file' => $replicaFilePath,
                    'url' => $disk->url($fileFolderMD5.'/'.$replicaFilename),
                    'type' =>$this->key,
                    'typeFile' => 'video',
                    'confirm' => 1,
                    'published' => $originalContent->published,
                    'targetClass' => $originalContent->targetClass,
                    'contentable_type' => $originalContent->contentable_type,
                    'contentable_id' => $originalContent->contentable_id,
                    'parent_id' => $originalContent->id,
                    'mime' => 'video/mp4',
                    'alt' => $info->title,
                ]
            );
            $content = Content::create($replicaFileInfo->toArray());
            //handle preview for video
            if($originalContent->preview && $originalContent->preview->id && $this->previewConverter && $content && $content->id){
                CreateReplicaJob::dispatch($this->previewConverter->forOriginalContentId($originalContent->preview->id)->asPreviewFor($content->id));
            }
            //run cache update for target class if exists
            $this->handleTargetModelCache($originalContent);
            return true;

        } catch (GuzzleException $e) {
            Log::error("Guzzle error during download file from youtube: " . $e->getMessage() );
            return false;
        }



    }

    public function getPossibleOriginalType(): string {
        return 'videoLinkYoutube';
    }
}
