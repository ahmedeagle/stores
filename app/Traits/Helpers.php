<?php 
namespace App\Traits;
use Carbon\Carbon;
use Storage;
use Intervention\Image\ImageManagerStatic as Image;

trait Helpers {
    
    public function handleErrorResponse($errors, $formAttributes){

        $resultErrors = [];

        foreach ($errors as $key => $values) {
            if(in_array($key, $formAttributes)){
                foreach ($values as $k => $v) {
                    array_push($resultErrors, $v);
                }
            }
        }
        return $resultErrors;
    }

    // public function saveImage($name, $img, $folder, $extensions){

    //     $image = 'data:image/jpeg;base64,' . $img;
    //     $name = substr($name, 0, 30);
    //     $ext = explode('/', explode(':', substr($image, 0, strpos($image, ';')))[1])[1];
    //     $fileName = $name . '.' . $ext;

    //     return response()->json(['image'=> $fileName]);

    //     if (!in_array($ext, $extensions)){
    //         return response()->json(['status'=> false, 'errNum' => 30, 'msg' => 'إمتداد الصورة غير صالح.']);
    //     }

    //     if (!file_exists($folder)) {
    //         mkdir($folder, 666, true);
    //     }
    //     Image::make($image)->save($folder.$fileName); 
    //     return $fileName;
    // }

    public function saveImage($folder, $photo, $name = ''){

        $img = str_replace('data:image/jpg;base64,', '', $photo);
        $img = str_replace('data:image/png;base64,', '', $img);
        $img = str_replace('data:image/gif;base64,', '', $img);
        $img = str_replace('data:image/jpeg;base64,', '', $img);
        $img = str_replace(' ', '+', $img);
        $data = base64_decode($img);
        $filename = $name == '' ? time() . '.png' : time() . '_' . $name . '.png';
        $path = $folder . $filename;
        file_put_contents($path, $data);
        return $filename;

    }

}