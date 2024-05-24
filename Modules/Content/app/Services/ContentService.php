<?php


namespace Modules\Content\Services;



use App\DataStructures\Content\ContentUpdateStructure;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;
use Modules\Content\Models\Content;
use Modules\Content\Http\Requests\StoreContentRequest;
use Modules\Content\Jobs\ClearUnconfirmedContentJob;
use Modules\Content\Jobs\CreateReplicaJob;
use Modules\Content\Jobs\RemoveVideoFromKinescopeJob;
use Modules\Content\Jobs\UploadVideoToKinescopeJob;
use Modules\Content\Services\ContentConverters\ContentConverterAbstract;

class ContentService
{
    const MIME_TYPES = ['txt' => 'text/plain',
        'htm' => 'text/html',
        'html' => 'text/html',
        'php' => 'text/html',
        'css' => 'text/css',
        'js' => 'application/javascript',
        'json' => 'application/json',
        'xml' => 'application/xml',
        'swf' => 'application/x-shockwave-flash',
        'flv' => 'video/x-flv',

        // images
        'png' => 'image/png',
        'jpe' => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'jpg' => 'image/jpeg',
        'gif' => 'image/gif',
        'bmp' => 'image/bmp',
        'ico' => 'image/vnd.microsoft.icon',
        'tiff' => 'image/tiff',
        'tif' => 'image/tiff',
        'svg' => 'image/svg+xml',
        'svgz' => 'image/svg+xml',
        'webp' => 'image/webp',

        // archives
        'zip' => 'application/zip',
        'rar' => 'application/x-rar-compressed',
        'exe' => 'application/x-msdownload',
        'msi' => 'application/x-msdownload',
        'cab' => 'application/vnd.ms-cab-compressed',

        // audio/video
        'mp3' => 'audio/mpeg',
        'mp4' => 'video/mp4',
        'qt' => 'video/quicktime',
        'mov' => 'video/quicktime',
        'webm' => 'video/webm',

        // adobe
        'pdf' => 'application/pdf',
        'psd' => 'image/vnd.adobe.photoshop',
        'ai' => 'application/postscript',
        'eps' => 'application/postscript',
        'ps' => 'application/postscript',

        // ms office
        'doc' => 'application/msword',
        'rtf' => 'application/rtf',
        'xls' => 'application/vnd.ms-excel',
        'ppt' => 'application/vnd.ms-powerpoint',

        // open office
        'odt' => 'application/vnd.oasis.opendocument.text',
        'ods' => 'application/vnd.oasis.opendocument.spreadsheet',];
    const STORAGE_DISK = 'content';
    const STORAGE_DISK_ORIGINAL = 'content-original';
    const IMAGE = 'image';

//с фронта приходят blob файлы - контент для отзывов (тренируемся на них)
    /**
     * с фронта приходят blob файлы - контент для отзывов (тренируемся на них)
      * в цикле
     * сохраняем сначала временный файл с методом saveTempFile(blob файл)
     * запускаем крон который через время удалит этот файл из временных, если он есть
     *
     *w
     *
     * когда от фронта приходит сохранение отзыва в ReviewResourceController::store()
     * тут обходим контент в цикле для данного отзыва
     * если контент не подтвержденный то сохраняем его постоянно(переименовываем файл)
     * запускаем в кроне PreviewsServices с настройками
     * PreviewsServices генерирует превью и сохраняет файл при помощи ContentService
     *
     *
     *
     *
     *
     *
     */


    //сохраняем временный файл
    //
    //
    //там где вызывается (в контроллере к примеру), настраиваем превью сервисы и передаем в крон



    protected ?Content $content = null;
    protected ?array $contentConverters = null;

    public function saveTempFiles(StoreContentRequest $request):array  {
        $filesInfo = [];
        if ($request->hasFile('files')) {

            $files = $request->file('files');
//save files
            foreach ($files as $file) {
                if(!$content = $this->saveTempFile( $file, $request )){
                    throw new \Exception('Error save upload files');
                }
                $filesInfo[] = $content->setVisible(['id', 'url', 'typeFile', 'confirm', 'published', ])->toArray();
            }
            return $filesInfo;
        }
        return $filesInfo;
    }

    public function saveVideoLink(StoreContentRequest $request):array  {
        $filesInfo = [];
        $vendorName = $this->getVendorNameFromVideoLink($request->videoLink);
        $idVideo = $this->getIdFromVideoLink($request->videoLink);
        if(!$vendorName || !$idVideo) return $filesInfo;

        $content = Content::create([
            'file' => '',
            'url' => $request->videoLink,
            'type' => 'original',
            'typeFile' => $vendorName,
            'mime' => 'hyperlink',
            'contentable_type'=> $request->contentable_type,
            'contentable_id' => $request->contentable_id,
            'is_preview_for' => ($request->is_preview_for) ?? '',
            'original_file_name' => $idVideo,

        ]);
        $filesInfo[] = $content->setVisible(['id', 'url', 'typeFile', 'confirm', 'published', ])->toArray();
        //create job for clear "forget" content
        ClearUnconfirmedContentJob::dispatch($content->id)->delay(now()->addHours(2));

        return $filesInfo;
    }


    public function saveTempFile( $fileBlob , StoreContentRequest $request):?Content {

        //if isset id, save to folder with name id
        //if not have id, that save in zero folder

        $extension = mb_strtolower($fileBlob->getClientOriginalExtension());
        $fileName = uniqid();
        $fileNameWithExtension = $fileName.'.'.$extension;

        $filePath =  md5(date('Y-m-d'));
//        $filePath =  'tmp';
        Storage::disk(self::STORAGE_DISK_ORIGINAL)->putFileAs($filePath, $fileBlob, $fileNameWithExtension);
        $file = $filePath.DIRECTORY_SEPARATOR.$fileNameWithExtension;
        if(!$fileType = $this->getFileType(Storage::disk(self::STORAGE_DISK_ORIGINAL)->path($file))) return null;

        //create job for clear "forget" content
        ClearUnconfirmedContentJob::dispatch($file)->delay(now()->addHours(2));


        $originalContent = Content::create([
            'file' => $file,
            'url' => Storage::disk(self::STORAGE_DISK_ORIGINAL)->url($file),
            'type' => 'original',
            'typeFile' => $fileType,
            'mime' => $this->getMime($file),
            'file_size' => Storage::disk(self::STORAGE_DISK_ORIGINAL)->size($file),
            'file_extension' => $extension,
            'contentable_type'=> $request->contentable_type,
            'contentable_id' => $request->contentable_id,
            'is_preview_for' => ($request->is_preview_for) ?? '',
            'original_file_name' => $request->original_file_name,

        ]);


        return $originalContent;
    }


    public function getFileType(string $file):?string {
        if (!$fileMime = $this->getMime($file))   return null; //<<<<<<<<<<<<
        if(!$fileMime = explode('/', $fileMime))    return null; //<<<<<<<<<<<<
        return $fileMime[0];
    }

    public function getMime( string $file):?string    {
        if(!$file = explode('.', $file))    return null; //<<<<<<<<<<<<
        $ext = strtolower(array_pop($file));
        if (!$fileMime = self::MIME_TYPES[$ext])   throw new \Exception('not set mime file'); //<<<<<<<<<<<<
        return $fileMime;
    }


    public function getPath():string    {
        return '';
    }

    public function store(array $contentInfoAsArray, string $contentable_type, string $contentable_id):self    {
        if(!$contentInfoForUpdate = $this->contentFromArrayToStructures($contentInfoAsArray, $contentable_type, $contentable_id)) return $this;
        return $this->updateFromArrayStructures( $contentInfoForUpdate, $contentable_type, $contentable_id );
    }

    public function update(array $contentInfoAsArray, string $contentable_type, string $contentable_id):self    {

        if(!$contentInfoForUpdate = $this->contentFromArrayToStructures($contentInfoAsArray, $contentable_type, $contentable_id)) return $this;
        return $this->updateFromArrayStructures( $contentInfoForUpdate, $contentable_type, $contentable_id );
    }

    public function updateFromArrayStructures( array $contentInfoForUpdate, string $contentable_type, string $contentable_id ):self    {

        $contentIds = $this->getContentIds($contentInfoForUpdate);

        $originalContents = Content::where('type', 'original')
            ->whereIn('id', $contentIds)
            ->with(['previewOriginal'])
            ->get();

        if($originalContents->count() > 0){
            foreach ($originalContents as $content){
                if(!$contentInfoFromFront = $contentInfoForUpdate[$content->id] ) continue;
                /**@var ContentUpdateStructure $contentInfoFromFront*/

                $content->update($contentInfoFromFront->toArray(['published', 'alt' ]));

            //handle alt for child
                if($replicas = Content::where('parent_id', $content->id)->get()){
                    $replicas->each(function ($rep) use ($content) {
                        $rep->update([
                            'published' => $content->published,
                            'alt' => ($content->alt) ?? '',
                        ]);
                    });
                }
                if(!$content->targetClass) {
                    $content->update([ 'targetClass' => $contentable_type, ]);
                }
                //handle generate replicas only if not confirmed original content
                if(!$content->confirm ){
                    $content->update(['confirm'=>1]);
                    $this->handleReplicasForOriginalContent($content);
                }

                //handle remove
                if($contentInfoFromFront->isDeleted) {
                    $this->removeContentById($content->id);
                    continue;
                }

//                if( $content->published !== $contentInfoFromFront->published ){
//                    $content->update([ 'published' => $contentInfoFromFront->published, ]);
//                }


               //handle preview (for video)
                $this->handlePreviewsForContent( $contentInfoFromFront );
                //update legacy cache data in doctor table

                //update original file


            }
        }
        if($targetForUpdateContent = $contentable_type::where('id', $contentable_id)->with('content')->first()){
            if(method_exists($targetForUpdateContent, 'contentCacheUpdate')){
                $targetForUpdateContent->contentCacheUpdate();
            }

        }

        return $this;
    }


    /**+ set jobs for create replicas of original content
     * @param Content $content
     * @return bool
     */

    protected function handleReplicasForOriginalContent(Content $content):bool {

        if($contentConverters = $this->getConvertersByTypeFile($content->typeFile)){
            if (config('kinescope.enable') && $content->typeFile === 'video'){
                UploadVideoToKinescopeJob::dispatch($content->id);
            }
            $content->left_handle_replicas = count($contentConverters);
            foreach ($contentConverters as $converter){
                //dont forget to run  Supervisor  php artisan queue:listen
                CreateReplicaJob::dispatch($converter->forOriginalContentId($content->id));
            }
        }
        return true;
    }


    /**
     * for original content handle previews
     * if not have preview from front - clear older preview in db
     * @param Model $originalContent
     * @return bool
     * @throws \Exception
     */

    protected function handlePreviewsForContent(ContentUpdateStructure $contentFromFront):bool {

        $originalPreviews = Content::where('is_preview_for', $contentFromFront->id)->get(); //possible more than one previews, if now change by new preview
        if($originalPreviews->count() === 0) $originalPreviews = null;
        if( !$contentFromFront->previewOriginal ){ //it means that clear preview
            if($originalPreviews){
                foreach ($originalPreviews as $preview){
                    $this->removeContentById($preview->id); //remove preview with previews of child content
                }
            }
            return true; //<<<<<<<<<<<<<<<<<<<
        }else{
            if ( !$originalPreviews ){
                throw new \Exception('Not have original preview');//<<<<<<<<<<<<<<<<<<<
            }else{
                foreach ($originalPreviews as $preview){
                    if($contentFromFront->previewOriginal->id !== $preview->id){
                        $this->removeContentById($preview->id); //remove preview with previews of child content
                        continue;   //<<<<<<<<<<<<<<<<<<<<
                    }
//                    if( (int)$preview->confirm === 1)   continue;   //<<<<<<<<<<<<<<<<<<<<
                    $preview->update(['confirm' => 1]);
                    //get replicas for original content
                    $replicas = Content::where('parent_id', $contentFromFront->id)->with('previewOriginal')->get();

                    if ($replicas->count() === 0)  continue;//<<<<<<<<<<<<<<<<<<<
                    //for every replica, if preview is change, update replica for new preview (in job)
                    foreach ($replicas as $replica) {
                        if ($replica->previewOriginal && $replica->previewOriginal->parent_id === $preview->id) continue;
                        if ($previewConverter = $this->getPreviewConverterForReplica($replica)) {
                            CreateReplicaJob::dispatch($previewConverter->forOriginalContentId($preview->id)->asPreviewFor($replica->id));
                        }

                    }
                }

            }

        }
        return true;
    }


    protected function contentFromArrayToStructures($contentInfoAsArray, string $contentable_type, string $contentable_id):array{
        $contentInfoStructures = [];
        $contentInfo = ['contentable_type' => $contentable_type, 'contentable_id' => $contentable_id];
        if(isset($contentInfoAsArray['id']) && $contentInfoAsArray['id']){
            $contentInfoAsArray = [$contentInfoAsArray];
        }
        foreach ($contentInfoAsArray as $info){
            if(isset( $info['previewOriginal']) && $info['previewOriginal'] ){
                $info['previewOriginal'] = new ContentUpdateStructure($info['previewOriginal'] + $contentInfo);
            }
            $contentInfoStructures[$info['id']] = new ContentUpdateStructure($info + $contentInfo);
        }
        return $contentInfoStructures;
    }

    public function updateTargetModelCache( string $targetClass, $targetId ):self    {
        try {
            $target = $targetClass::where('id', $targetId)->with(['content'])->first();
            if(!$target) return $this;
            if(isset($target->content_cache)) $target->update([]);

        }catch (\Exception $e){
            //todo handle error
        }

        return $this;
    }


    protected function getContentIds( array $contentStructures):array{
        $contentIds = [];
        foreach ($contentStructures as $structure){
            /**@var ContentUpdateStructure $structure*/
            $contentIds[] = $structure->id;
        }
        return array_unique($contentIds);
    }

    public function addContentConverter( ContentConverterAbstract $converter, string $originalTypeFile = '' ):self    {
        if(!$originalTypeFile) $originalTypeFile = $converter->getPossibleOriginalType();
        if(!isset($this->contentConverters[$originalTypeFile])) $this->contentConverters[$originalTypeFile] = [];
        $this->contentConverters[$originalTypeFile][] = $converter;
        return $this;
    }

    protected function getConvertersByTypeFile(string $typeFile ):?array{
        return (isset($this->contentConverters[$typeFile])) ? $this->contentConverters[$typeFile] : [];
    }



    protected function getConverterForReplica( Model $replica ):?ContentConverterAbstract{
        if ( !$replica->typeFile || !$replica->type) {
            //if exists replica by not have converter for convert to this replica - its error
            throw new \Exception('Not have replica->typeFile or replica->type ');
        }
        if(!$this->contentConverters || !isset($this->contentConverters[$replica->typeFile]) || !$this->contentConverters[$replica->typeFile]) return null;
        foreach ($this->contentConverters[$replica->typeFile] as $converter){
            /**@var ContentConverterAbstract $converter*/
            if($converter->key === $replica->type){
                return $converter;
            }
        }
        return null;
    }

    protected function getPreviewConverterForReplica( Model $replica ):?ContentConverterAbstract{
        $replicaConverter = $this->getConverterForReplica($replica);
        return ($replicaConverter && $replicaConverter->previewConverter) ? $replicaConverter->previewConverter : null;
    }

    public function diskName():string    {
        return self::STORAGE_DISK;
    }

    public function diskNameOriginal():string    {
        return self::STORAGE_DISK_ORIGINAL;
    }

    public function getStorageDiskNameDefault():string{
        return self::STORAGE_DISK;
    }

    public function getStorageDisk():Filesystem   {
        return $this->storageDisk();
    }

    public function getOriginalDisk():Filesystem   {
        return Storage::disk(self::STORAGE_DISK_ORIGINAL);
    }

    protected function storageDisk():Filesystem {
        return Storage::disk(self::STORAGE_DISK);
    }

    public function removeContentById(string $contentId):bool {

        //if content already remove return
        //clear original file

        if (!$content = Content::find($contentId)) {
            throw new \Exception('Review not found');
        }

        if (config('kinescope.enable') && $content->type === 'kinescope'){
            RemoveVideoFromKinescopeJob::dispatch($content);
            return true;
        }

        $this->storageDisk()->delete($content->file);
        //clear replicas files recursive
        if($replicas = Content::where('parent_id', $contentId)->get()){
            foreach ($replicas as $replica){
                $this->removeContentById($replica->id);
            }
        }

        //remove content preview if exists
        if($previews = Content::where('is_preview_for', $contentId)->get()){
            foreach ($previews as $preview){
                $this->removeContentById($preview->id);
            }
        }

        $content->delete();
        return true;
    }

    public function destroy():self     {

        return $this;
    }


    protected function getVendorNameFromVideoLink($url):?string{
        // YouTube video URL pattern
        $youtubePattern = '/^(https?:\/\/)?(www\.)?(youtube\.com\/watch\?v=|youtu\.be\/|youtube\.com\/embed\/)([a-zA-Z0-9_-]+)(.*)?$/';
        if(preg_match($youtubePattern, $url) === 1){// Check if the URL matches the YouTube pattern
            return 'videoLinkYoutube';
        }

        return '';
    }

    protected function getIdFromVideoLink($url):?string{
        $pattern = '/^https:\/\/(?:(?:www|m)\.)?(?:youtube\.com\/watch\?v=|youtu.be\/|youtube\.com\/embed\/)([a-zA-Z0-9_-]+)(?:&.*)?$/';
        if (preg_match($pattern, $url, $matches)) {
            return $matches[1];
        } else {
            return null;
        }
    }


    protected function removeVideoFromKinescope(){

    }

}

