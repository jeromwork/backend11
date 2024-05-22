<?php

namespace Modules\Content\Services;

use App\DataStructures\Content\CreateReplicaContentStructure;
use App\DataStructures\Kinescope\KinescopeReplicaStructure;
use Modules\Content\app\Models\Content;



class Kinescope
{

    public function uploadContent(Content $originalContent):?array{
        if(!$originalContent || !$originalContent->id ) return null;
        if($originalContent->typeFile !== 'video' || !config('kinescope.enable')) return null;
//        $url = (empty($_SERVER['HTTPS']) ? 'http' : 'https').'://'.$_SERVER['HTTP_HOST'].$originalContent->url;


        $ch = curl_init();

        $headers = array(
            'Authorization: Bearer ' . config('kinescope.apiToken'),
            'X-Parent-ID: ' . config('kinescope.idProject'),
            'X-Video-Title: ' . $originalContent->id,
            'X-Video-Description: ' . ($originalContent->alt) ?? '',
            'X-File-Name: ' . $originalContent->id,
//            'Content-type: ' . $originalContent->mime,
//            'X-Video-Trim: {"start": 1, "length": 2}'
        );


        $fileOriginalFullPath = (new ContentService())->getOriginalDisk()->path($originalContent->file);
//        $post_fields = array(
//            'data-binary' => new CURLFile($fileOriginalFullPath), // Use CURLFile for binary file uploads
//            // Add any other parameters if required
//        );

        curl_setopt($ch, CURLOPT_URL, 'https://uploader.kinescope.io/v2/video');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
//        curl_setopt($ch, CURLOPT_POSTFIELDS, fopen($fileOriginalFullPath, 'r'));
        curl_setopt($ch, CURLOPT_INFILE, fopen($fileOriginalFullPath, 'r'));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        $response = curl_exec($ch);
        curl_close($ch);
        if ($response === false) {
            //todo @telegram
            return null;
        }
        $response = json_decode($response, true);
        if(isset($response['data']) && $response['data'] && $response['data']['id']  && $response['data']['play_link']){
            return $response['data'];
        }
        return  null;
    }

    public function delete( string $contentKinescopeId ):bool{
        $curl = curl_init();

// Set curl options
        curl_setopt_array($curl, array(
            CURLOPT_URL =>  'https://api.kinescope.io/v1/videos/'.$contentKinescopeId,
            CURLOPT_RETURNTRANSFER => true, // Return the response as a string instead of outputting it directly
            CURLOPT_FOLLOWLOCATION => true, // Follow any redirects
            CURLOPT_CUSTOMREQUEST => 'DELETE', // Set the request method to DELETE
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_HTTPHEADER => ['Authorization: Bearer ' . config('kinescope.apiToken'),],
        ));

// Execute curl and store the response
        $response = curl_exec($curl);

// Check for errors
        if(curl_errno($curl) || !$response) {
            $error_message = curl_error($curl);
            // todo @telegram errors
            return false;
        }
        $response = json_decode($response, true);
        if(isset($response['data']) && $response['data'] && $response['data']['success']){
            return true;
        }
        return false;
    }



    public function getReplicaInfo(string $contentKinescopeId, string $key):?KinescopeReplicaStructure{
        if(!$videoInfo = $this->getKinescopeVideoInfoForContent($contentKinescopeId))   return null; //<<<<<<<<<<<<<<<<<<<<<<<<<<<<
        if(!isset($videoInfo['data']) ||!isset($videoInfo['data']['status']))   throw new \Exception('Not correct kinescope data by Api');
        if($videoInfo['data']['status'] !== 'done')  return null;
        if(!$replicas = $videoInfo['data']['assets']) throw new \Exception('Doesnt have assets in kinescope data');
        foreach ($replicas as $replica){
            if(isset($replica['quality']) && $replica['quality'] === $key) return new KinescopeReplicaStructure($replica);
        }
        return null;
    }

    public function getKinescopeVideoInfoForContent(string $contentKinescopeId):?array{
        $curl = curl_init();

// Set curl options
        curl_setopt_array($curl, array(
            CURLOPT_URL =>  'https://api.kinescope.io/v1/videos/'.$contentKinescopeId,
            CURLOPT_RETURNTRANSFER => true, // Return the response as a string instead of outputting it directly
            CURLOPT_FOLLOWLOCATION => true, // Follow any redirects
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_HTTPHEADER => ['Authorization: Bearer ' . config('kinescope.apiToken'),],
        ));
        $response = curl_exec($curl);// Execute curl and store the response

        if(curl_errno($curl) || !$response) {// Check for errors
            $error_message = curl_error($curl);
            // todo @telegram errors
            return null;
        }
        try {
            return json_decode($response, true);
        }catch (\Exception $e){
            // todo @telegram errors
        }

        return null;
    }



}
