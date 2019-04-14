<?php
use \Eventviva\ImageResize;

class Image_Library{
    
    
    public static function ImageUpload($destinationPath,$file){
        if(!empty($file)){
            $rules = array(
                'file' => 'required|mimes:png,gif,jpeg,jpg|max:20000'
            );
            $validator = \Validator::make( Input::all() , $rules);
            if($validator->passes()){
                //$destinationPath = self::FindUploadLocation($usertype);
                //$destinationPath = public_path('assets/upload/user/');
                $filename = str_random(32) .time(). '.' . 'png';
                $uploaded = $file->move($destinationPath, $filename);
                if($uploaded){
                    return $filename;
                }else{
                    return FALSE;
                }
            }else{
                return FALSE;
            }
        }else{
            return FALSE;
        }
    }

    public static function CloudinaryImageUpload($filepath){
        //$up = \Cloudinary\Uploader::upload($filepath,array("crop" => "scale", "width" => 100,"format" => "png")); 
        $up = \Cloudinary\Uploader::upload($filepath);
        if(!empty($up)){
            return $up['secure_url']; 
        }else{
            return FALSE;
        }
    }
    public static function CloudinaryImageUploadResize($filepath,$width,$height){
        //$up = \Cloudinary\Uploader::upload($filepath,array("crop" => "scale", "width" => 100,"format" => "png")); 
        $up = \Cloudinary\Uploader::upload($filepath,array("width" => $width, "height" => $height));
        if(!empty($up)){
            return $up['secure_url']; 
        }else{
            return FALSE;
        }
    }
    
    public static function CloudinaryUpload(){
        $file = Input::file('file');
        if(!empty($file)){
            $rules = array(
                'file' => 'required|mimes:png,gif,jpeg,jpg|max:20000'
            );
            $validator = \Validator::make( Input::all() , $rules);
            if($validator->passes()){
                $upload = self::CloudinaryImageUpload($file->getPathName());     
                if(!empty($upload)){
                    return $upload; 
                }else{
                    return FALSE;
                }
            }else{
                return FALSE;
            }
        }else{
            return FALSE;
        }
    }

    public static function CloudinaryUploadWithResize($width,$height){
        $file = Input::file('file');
        if(!empty($file)){
            $rules = array(
                'file' => 'required|mimes:png,gif,jpeg,jpg|max:20000'
            );
            $validator = \Validator::make( Input::all() , $rules);
            if($validator->passes()){
                //$upload = self::CloudinaryImageUpload($file->getPathName());
                $upload = self::CloudinaryImageUploadResize($file->getPathName(),$width,$height);
                if(!empty($upload)){
                    return $upload; 
                }else{
                    return FALSE;
                }
            }else{
                return FALSE;
            }
        }else{
            return FALSE;
        }
    }
    public static function CloudinaryUploadFile($file){
        // $file = Input::file('file');
        if(!empty($file)){
            $rules = array(
                'clinic_price' => 'required|mimes:png,gif,jpeg,jpg|max:20000'
            );
            $validator = \Validator::make( Input::all() , $rules);
            if($validator->passes()){
                $upload = self::CloudinaryImageUpload($file->getPathName());     
                if(!empty($upload)){
                    return $upload; 
                }else{
                    return FALSE;
                }
            }else{
                return FALSE;
            }
        }else{
            return FALSE;
        }
    }
    public static function CloudinaryUploadFileWithResizer($file, $width, $height){
        // $file = Input::file('file');
        if(!empty($file)){
            $rules = array(
                'file' => 'required|mimes:png,gif,jpeg,jpg|max:20000'
            );
            $validator = \Validator::make( Input::all() , $rules);
            if($validator->passes()){
                $upload = self::CloudinaryImageUploadResize($file->getPathName(), $width, $height);
                if(!empty($upload)){
                    return $upload; 
                }else{
                    return FALSE;
                }
            }else{
                return FALSE;
            }
        }else{
            return FALSE;
        }
    }
    
    public static function ImageResizing($usertype,$with){
        $file = Input::file('file');
        $destinationPath = self::FindUploadLocation($usertype);
        $imageUpload = self::ImageUpload($destinationPath,$file);
        if($imageUpload){
            $image = new ImageResize($destinationPath.$imageUpload);
            $image->resizeToWidth($with);
            $imageSaved = $image->save($destinationPath.$imageUpload, IMAGETYPE_PNG);
            if($imageSaved){
                return $imageUpload;
            }else{
                return FALSE;
            }
        }else{
            return FALSE;
        }
    }

    

    private static function FindUploadLocation($usertype){
        if($usertype ==1){
            $destinationPath = public_path('assets/upload/user/');
        }elseif($usertype ==2){
            $destinationPath = public_path('assets/upload/doctor/');
        }elseif($usertype ==3){
            $destinationPath = public_path('assets/upload/clinic/');
        }
        return $destinationPath;
    }

    



    private static function cleanFileName($fileName){
        //remove blanks
        $fileName = preg_replace('/\s+/', '', $fileName);
        //remove charactes
        $fileName = preg_replace("/[^A-Za-z0-9_-\s.]/", "", $fileName);

    return $fileName;
    }
    
    
    
    public static function ImageResizing1(){
        $file = Input::file('file');
        echo '<pre>';
        print_r($file);
        echo '</pre>'; echo '<br>';
        
        $rules = array(
            'file' => 'required|mimes:png,gif,jpeg,jpg|max:20000'
        );
        $validator = \Validator::make( Input::all() , $rules);

        if($validator->passes()){
           
            //$originalName = $file->getClientOriginalName();
            
            
            //$tempPath = $file->getRealPath().$originalName;
            $destinationPath = public_path('assets/upload/user/');
            //$filename = str_random(32) .time(). '.' . $file->getClientOriginalExtension();
            $filename = str_random(32) .time(). '.' . 'png';
            $uploaded = $file->move($destinationPath, $filename);
            
          


            $image = new ImageResize($destinationPath.$filename);
            //$image = new ImageResize($filename);
            $image->resizeToWidth(150);
            //$image->save($destinationPath.$filename);
            $image->save($destinationPath.$filename, IMAGETYPE_PNG);
        }else{
            echo 'fail';
        }
      
       
    }
}
