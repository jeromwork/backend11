<?php


namespace Modules\Content\Services\ContentConverters;


use App\DataStructures\Content\CreateReplicaContentStructure;
use FFMpeg\Format\Video\X264;
use Illuminate\Support\Facades\Storage;
use Modules\Content\Models\Content;
use Modules\Content\Jobs\CreateReplicaJob;
use Modules\Content\Services\ContentService;
//use Modules\Reviews\Jobs\CreatePreviewJob;
use ProtoneMedia\LaravelFFMpeg\Exporters\EncodingException;
use ProtoneMedia\LaravelFFMpeg\Filters\WatermarkFactory;
use ProtoneMedia\LaravelFFMpeg\Support\FFMpeg;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Config;

class VideoContentConverter extends ContentConverterAbstract
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


            //get fresh original and preview content from db
            $originalContent = Content::where('id', $this->originalContentId)->with('preview')->first();

            if(!$originalContent || !$originalContent->id || $originalContent->typeFile !== 'video') return false;
//            if( config('kinescope.enable')) {
//                //run job with while download files from kinescope
//                return $this->getVideosReplicasFromKinescope($originalContent);
//
//                return false;
//            }
            $content = $this->convertVideoContent($originalContent);
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

    protected function convertVideoContent(Content $originalContent):?Content{
        $contentService = new ContentService();
        $disk = $contentService->getStorageDisk();
        $fileOriginalFullPath = $contentService->getOriginalDisk()->path($originalContent->file);
        if( !file_exists($fileOriginalFullPath) ) {
            throw new \Exception('Not exists original file');
        }
        $fileInfo= pathinfo($originalContent->file);
        $originalFileExtension = mb_strtolower($fileInfo['extension']);
        $originalFileFolder = $fileInfo['dirname'];
        if(!in_array($originalFileExtension, $this->possibleExtensions)) return null;
        $extension = ($this->extensionPreview && in_array($this->extensionPreview, $this->possibleExtensions)) ? $this->extensionPreview : $originalFileExtension;
        $previewFilename = uniqid().'.'.$extension;
        $previewFileFullPath = $originalFileFolder.DIRECTORY_SEPARATOR.$previewFilename;
        $previewFileFullUrl = $originalFileFolder.DIRECTORY_SEPARATOR.$previewFilename;
//            $previewFileFullPath = $disk->path($previewFile);
//https://github.com/protonemedia/laravel-ffmpeg
        $ffmpeg = FFMpeg::fromDisk($contentService->diskNameOriginal())
            ->open($originalContent->file)
//                ->addWatermark(function(WatermarkFactory $watermark) {
//                    $watermark->openUrl('https://eastclinic.ru/favicon.png?v=2');
//                })
            ->export()
            ->toDisk($contentService->diskName());
        $handler = $this->getFormat($extension);
        $ffmpeg->inFormat(new $handler());
        if($this->width && $this->height) {
            $ffmpeg->resize($this->width, $this->height);
        }
        if (!$disk->exists($originalFileFolder)) {
            // If the folder doesn't exist, create it
            $disk->makeDirectory($originalFileFolder);
        }
        $ffmpeg->save($previewFileFullPath);

        $previewFileInfo = new CreateReplicaContentStructure(
            [
                'file' => $previewFileFullPath,
                'url' => $disk->url($previewFileFullUrl),
                'type' =>$this->key,
                'typeFile' => $contentService->getFileType($previewFilename),
                'confirm' => 1,
                'published' => $originalContent->published,
                'targetClass' => $originalContent->targetClass,
                'contentable_type' => $originalContent->contentable_type,
                'contentable_id' => $originalContent->contentable_id,
                'parent_id' => $originalContent->id,
                'mime' => $contentService->getMime($previewFilename),

            ]
        );

        return Content::create($previewFileInfo->toArray());
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


}
