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

    public function saveImage($name, $img, $folder, $extensions){

        $image = 'data:image/png;base64,' . $img;
        $name = substr($name, 0, 30);
        $ext = explode('/', explode(':', substr($image, 0, strpos($image, ';')))[1])[1];
        $fileName = $name . '.' . $ext;

        if (!in_array($ext, $extensions)){
            return response()->json(['status'=> false, 'errNum' => 30, 'msg' => 'إمتداد الصورة غير صالح.']);
        }

        if (!file_exists($folder)) {
            mkdir($folder, 666, true);
        }
        Image::make($image)->save($folder.$fileName); 
        return $fileName;
    }

}