<?php


namespace Modules\Content\Services\ContentConverters;


use App\DataStructures\Content\CreateReplicaContentStructure;
use FFMpeg\Format\Video\X264;
use Illuminate\Support\Facades\Storage;
use Modules\Content\Models\Content;
use Modules\Content\Jobs\CreateReplicaJob;
use Modules\Content\Services\ContentService;
use Modules\Content\Services\Kinescope;
use ProtoneMedia\LaravelFFMpeg\Exporters\EncodingException;
use ProtoneMedia\LaravelFFMpeg\Filters\WatermarkFactory;
use ProtoneMedia\LaravelFFMpeg\Support\FFMpeg;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Config;

class VideoKinescopeContentConverter extends ContentConverterAbstract
{



    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 10;

    protected array $possibleExtensions = ["mp4", "mov", 'webm'];
    protected string $extensionPreview = 'webm';

    const EXTENSION_FFMPEG = [
        'webm' => \FFMpeg\Format\Video\WebM::class,
        'mp4' => X264::class,
        ];



    public function generateReplicas():bool {
        if( !$this->originalContentId || !$this->key)       return false;
        try {
            if(!$originalContent = Content::where('id', $this->originalContentId)->first()) return false; //todo @telegram error
            if(Content::where('parent_id', $this->originalContentId)->where('type', $this->key)->first()) return true; //if replica already exists - return
            $timeout = round($originalContent->file_size / 1000000); //for 160 MB video == 1 min duration timeout 160 sec
            if($timeout < 5)  $timeout = 5; //todo @telegram error
            //get fresh original and preview content from db
            $kinescopeContent = null;
            $attempt = 0;
            //while upload
            while (true) {
                if ($kinescopeContent = Content::where('parent_id', $this->originalContentId)->where('type', 'kinescope')->with('preview')->first()) break;
                if($attempt >10) return true;
                $attempt++;
                sleep($timeout); // You can adjust the sleep time as needed
            }
            if(!$kinescopeContent)  return false;//<<<<<<<<<<<<<<<<<<<<<<
            sleep($timeout); // wait handle
            $processKinescopeConvert = true;
            $attempt = 0;
            while (true) {
                if ($kinescopeReplicaInfo = (new Kinescope())->getReplicaInfo($kinescopeContent->file, $this->key)) break;
                if($attempt >10) return true; //todo @telegram error or this quality not exists
                $attempt++;
                // Sleep for a while before making the next request (to avoid overwhelming the API)
                sleep($timeout); // You can adjust the sleep time as needed
            }

            $extension = $kinescopeReplicaInfo->filetype;
            $contentService = new ContentService();
            $disk = $contentService->getStorageDisk();
            $filePath =  md5(date('Y-m-d'));
            $fileUniqueName = uniqid();
            $fileNameWithExtension = $fileUniqueName.'.'.$extension;

            // Get the file content from the URL
            $fileContent = file_get_contents($kinescopeReplicaInfo->download_link);

            if ($fileContent === false) {
                throw new \Exception('Failed to fetch the file from the URL.');
            }
            $fileName = $filePath.DIRECTORY_SEPARATOR.$fileNameWithExtension;
            // Save the file content using Laravel's Storage
            $disk->put($fileName, $fileContent);

            $previewFileInfo = new CreateReplicaContentStructure(
                [
                    'file' => $fileName,
                    'url' => $disk->url($fileName),
                    'type' =>$this->key,
                    'typeFile' => 'video',
                    'confirm' => 1,
                    'published' => $originalContent->published,
                    'targetClass' => $originalContent->targetClass,
                    'contentable_type' => $originalContent->contentable_type,
                    'contentable_id' => $originalContent->contentable_id,
                    'parent_id' => $originalContent->id,
                    'is_preview_for'=> '',
                    'mime' => $contentService->getMime($fileName),
                    'file_size' => $kinescopeReplicaInfo->file_size,
                    'file_extension' => $kinescopeReplicaInfo->filetype,
                    'original_file_name' => $originalContent->original_file_name,
                    'alt' => $originalContent->alt,
                ]
            );

            $content = Content::create($previewFileInfo->toArray());
            $originalContent->update(['left_handle_replicas', $originalContent->left_handle_replicas--]); //for observe complete jobs all converters

            //handle preview for video
            if($originalContent->preview && $originalContent->preview->id && $this->previewConverter && $content && $content->id){
                CreateReplicaJob::dispatch($this->previewConverter->forOriginalContentId($originalContent->preview->id)->asPreviewFor($content->id));
            }
            //run cache update for target class if exists
            $this->handleTargetModelCache($originalContent);

        }catch (\Throwable $e){
            error_log($e->getMessage());
        }
        return true;
    }



    public function getPossibleOriginalType():string  {
        return 'video';
    }


    protected function getFormat(string $extension ):string   {
        return self::EXTENSION_FFMPEG[$extension];
    }

    public function withPreview(ContentConverterAbstract $converter):self  {
        $this->previewConverter = $converter;
        return $this;
    }

    /**
     * @param string $key [360p|480p|720p|1080p]
     * @return $this
     */





}
