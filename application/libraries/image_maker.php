<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Image_Maker { /* Base : http://nleach.com/post/4315166514/activity-feed-in-redis-using-php */
 
    public function __construct(){
    }
    
    public function put_image($from_filename, $image_filename, $to_filename)
    {
        list($image, $type) = $this->createImageFromFile($from_filename);
        
        if($image) {
            $ext = $this->getFileType($to_filename);
            if($ext) {
                switch($ext) {
                    case 'jpg': $type = IMAGETYPE_JPEG; break;
                    case 'gif': $type = IMAGETYPE_GIF; break;
                    case 'png': $type = IMAGETYPE_PNG; break;
                }
            }
            
            list($width, $height) = getimagesize($from_filename); 
            
            $put_im = @imagecreatefrompng($image_filename);
            if($put_im)
            {
                list($put_w, $put_h) = getimagesize($image_filename); 
                
                imagecopyresampled(
                  $image, $put_im,             // destination, source
                  round(($width - $put_w)/2), round(($height - $put_h)/2), 0, 0,           // dstX, dstY, srcX, srcY
                  $put_w, $put_h,                               // dstW, dstH
                  $put_w, $put_h);    // srcW, srcH*/
            }
            
            if($type == IMAGETYPE_GIF) {
               $res = ImageGIF($image, $to_filename);
            } else if($type == IMAGETYPE_PNG) {
               $res = ImagePNG($image, $to_filename);
            } else if($type == IMAGETYPE_JPEG) {
               $res = ImageJPEG($image, $to_filename, 100);
            } else {
               $res = false;
            }
        }
    }
    
    public function blur($from_filename, $to_filename, $blur_count = 6) {
        list($image, $type) = $this->createImageFromFile($from_filename);
        if($image) {
            for ($x=1; $x<=$blur_count; $x++)
                imagefilter($image, IMG_FILTER_GAUSSIAN_BLUR);
            
            $ext = $this->getFileType($to_filename);
            if($ext) {
                switch($ext) {
                    case 'jpg': $type = IMAGETYPE_JPEG; break;
                    case 'gif': $type = IMAGETYPE_GIF; break;
                    case 'png': $type = IMAGETYPE_PNG; break;
                }
            }
            
            if($type == IMAGETYPE_GIF) {
               $res = ImageGIF($image, $to_filename);
            } else if($type == IMAGETYPE_PNG) {
               $res = ImagePNG($image, $to_filename);
            } else if($type == IMAGETYPE_JPEG) {
               $res = ImageJPEG($image, $to_filename, 100);
            } else {
               $res = false;
            }
        }
        
        return $res ? true : false;
    }
    
    public function combine($w, $h, $to_filename, $images)
    {
        $count = count($images);
        if($count > 4) $count = 4;
        
        $ni = imageCreateTrueColor($w, $h);

        $white = imagecolorallocate($ni, 0, 0, 0); 
        imagefilledrectangle( $ni, 0, 0, $w, $h, $white );
        
        $_w = round($w / 2);
        $_h = round($h / 2);
        $_x = 0;
        $_y = 0;
        
        $_sx = 158;
        $_sy = 80;
        $_sw = 200;
        $_sh = 200;
        
        for($i=0;$i<$count;$i++) {
            $image = current($images);
            
            $im = @imagecreatefromjpeg($image);
            if($im)
            {
                imagecopyresampled(
                  $ni, $im,             // destination, source
                  $_x, $_y, $_sx, $_sy,           // dstX, dstY, srcX, srcY
                  $_w, $_h,                               // dstW, dstH
                  $_sw, $_sh);    // srcW, srcH*/
            }
               
            $_x += $_w;
            
            if($_x + $_w > $w) {
                $_x = 0;
                $_y = $_y + $_h;
            }
            
            next($images);
        } 
        
        ImageJPEG($ni, $to_filename, 100);
        
        return true;
    }

    private function createImageFromFile($from) {
        $type = exif_imagetype($from);
        
        switch ($type) {
            case IMAGETYPE_GIF:
                    $im = ImageCreateFromGIF($from);
                break;
            case IMAGETYPE_JPEG:
                    $im = ImageCreateFromJPEG($from);       
                break;
            case IMAGETYPE_PNG:
                    $im = ImageCreateFromPNG($from);
                break;
                    $im = false;
                break;
        }
        
        return array($im, $type);
    }
    
    function getFileType($filename) {
        $result = strtolower(substr($filename, strrpos($filename,".")+1));
        if(empty($result)) return false;
        
        return $result;
    }
}