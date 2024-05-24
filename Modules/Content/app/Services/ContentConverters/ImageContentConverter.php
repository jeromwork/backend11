<?php


namespace Modules\Content\Services\ContentConverters;


use App\DataStructures\Content\ContentFileInfoStructure;
use App\DataStructures\Content\CreateReplicaContentStructure;
use Illuminate\Support\Facades\Storage;
use Illuminate\Contracts\Filesystem\Filesystem;
//use Intervention\Image\Image;
//use Intervention\Image\Facades\Image;
use Intervention\Image\Laravel\Facades\Image;
//use Intervention\Image\ImageManagerStatic as Image;
use Modules\Content\Models\Content;
use Modules\Content\Services\ContentService;
use function Symfony\Component\Finder\name;
use Illuminate\Support\Facades\File;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class ImageContentConverter extends ContentConverterAbstract
{


    public function generateReplicas():bool {

        if( !$this->originalContentId || !$this->key)       return false;
        try {
            $contentService = new ContentService();
            //get fresh original and preview content from db
            $originalContent = Content::where('id', $this->originalContentId)->first();
            if(!$originalContent || !$originalContent->id || $originalContent->typeFile !== 'image' ) return false;

            $disk = $contentService->getStorageDisk();
            $fileOriginalFullPath = $contentService->getOriginalDisk()->path($originalContent->file);
            if( !file_exists($fileOriginalFullPath) ) {
                throw new \Exception('Not exists original file');
            }
            $fileInfo= pathinfo($originalContent->file);
            $originalFileExtension = mb_strtolower($fileInfo['extension']);
            $originalFileFolder = $fileInfo['dirname'];
            if(!in_array($originalFileExtension, ["jpg", "png", "jpeg", 'webp'])) return false;
            $extension = ($this->extensionPreview && in_array($this->extensionPreview, ["jpg", "png", "jpeg", 'webp'])) ? $this->extensionPreview : $originalFileExtension;
            //todo possible doubles unique id, i think so, check it later
            $previewFilename = uniqid().'.'.$extension;

            $preview = Image::read($fileOriginalFullPath);

            if($this->width && $this->height) {
                $preview->resize($this->width, $this->height);
            }
            $preview ->encodeByExtension($extension, $this->quality);

            $previewFile = $originalFileFolder.DIRECTORY_SEPARATOR.$previewFilename;
            $previewFileFullPath = $disk->path($previewFile);
            if (!$disk->exists($originalFileFolder)) {
                // If the folder doesn't exist, create it
                $disk->makeDirectory($originalFileFolder);
            }
            $preview->save( $previewFileFullPath );



            $previewFileInfo = new CreateReplicaContentStructure(
                [
                    'file' => $previewFile,
                    'url' => $disk->url($previewFile),
                    'type' =>$this->key,
                    'typeFile' => $contentService->getFileType($previewFile),
                    'confirm' => 1,
                    'published' => $originalContent->published,
                    'targetClass' => $originalContent->targetClass,
                    'contentable_type' => $originalContent->contentable_type,
                    'contentable_id' => $originalContent->contentable_id,
                    'parent_id' => $originalContent->id,
                    'is_preview_for'=>($this->parentReplicaId) ?? '',
                    'mime' => $contentService->getMime($previewFile),
                    'alt' => ($originalContent->alt) ?? '',
                ]
            );
            Content::create($previewFileInfo->toArray());
            $originalContent->update(['left_handle_replicas', $originalContent->left_handle_replicas--]); //for observe complete jobs all converters
            //update content cache
            if($originalContent->targetClass && method_exists($originalContent->targetClass, 'contentCacheUpdate')){
                if($target = $originalContent->targetClass::where('id', $originalContent->contentable_id)->first()){
                    $target->contentCacheUpdate();
                }
            }



        }catch (\Throwable $e){
            error_log($e->getMessage());
        }
        return true;
    }



    public function getPossibleOriginalType():string  {
        return 'image';
    }

}
