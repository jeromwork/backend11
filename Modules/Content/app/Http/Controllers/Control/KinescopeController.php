<?php

namespace Modules\Content\Http\Controllers\Control;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\Content\Http\Requests\StoreContentRequest;

class KinescopeController extends \Illuminate\Routing\Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function getUrl(StoreContentRequest $request)
    {
        $name = $request->name;
        $size = $request->size;

        $KINESCOPE_API_TOKEN = 'тут токен';
        $parent_id = "id проекта или папки";
        $headers = array(
            'Content-Type: application/json',
            'Authorization: Bearer ' . $KINESCOPE_API_TOKEN,
        );
        $data = json_encode(array(
            'filesize' => (int) $size,
            'type' => 'video',
            'title' => $name,
            'parent_id' => $parent_id,
            'filename' => $name,
            'description' => 'video description',
        ));

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_URL, "https://uploader.kinescope.io/v2/init");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        $result = json_decode(curl_exec($ch), true);
        curl_close($ch);

        return redirect(null, 201)->away($result['data']['endpoint']);
    }

}
