<?php

class qqUploadedFileXhr {
    /**
     * Save the file to the specified path
     * @return boolean TRUE on success
     */
    function save($path) {    
        $input = fopen("php://input", "r");
        $temp = tmpfile();
        $realSize = stream_copy_to_stream($input, $temp);
        fclose($input);
        
        if ($realSize != $this->getSize()){            
            return false;
        }
        
        $target = fopen($path, "w");        
        fseek($temp, 0, SEEK_SET);
        stream_copy_to_stream($temp, $target);
        fclose($target);
        
        return true;
    }
	
    function getName() {
        return $_GET['qqfile'];
    }
	
    function getSize() {
        if (isset($_SERVER["CONTENT_LENGTH"])){
            return (int)$_SERVER["CONTENT_LENGTH"];            
        } else {
            throw new Exception('잘못된 파일입니다.');
        }      
    }   
}

/**
 * Handle file uploads via regular form post (uses the $_FILES array)
 */
class qqUploadedFileForm {  
    /**
     * Save the file to the specified path
     * @return boolean TRUE on success
     */
    function save($path) {
        if(!move_uploaded_file($_FILES['qqfile']['tmp_name'], $path)){
            return false;
        }
        return true;
    }
    function getName() {
        return $_FILES['qqfile']['name'];
    }
    function getSize() {
        return $_FILES['qqfile']['size'];
    }
}

class qqFileUploader {
    private $allowedExtensions = array();
    private $sizeLimit = 10485760;
    private $file;

    function __construct(array $allowedExtensions = array(), $sizeLimit = 10485760){        
        $allowedExtensions = array_map("strtolower", $allowedExtensions);
            
        $this->allowedExtensions = $allowedExtensions;        
        $this->sizeLimit = $sizeLimit;
        
        $this->checkServerSettings();       

        if (isset($_GET['qqfile'])) {
            $this->file = new qqUploadedFileXhr();
        } elseif (isset($_FILES['qqfile'])) {
            $this->file = new qqUploadedFileForm();
        } else {
            $this->file = false; 
        }
    }
    
    private function checkServerSettings(){        
        $postSize = $this->toBytes(ini_get('post_max_size'));
        $uploadSize = $this->toBytes(ini_get('upload_max_filesize'));        
        if ($postSize < $this->sizeLimit || $uploadSize < $this->sizeLimit){
            $size = max(1, $this->sizeLimit / 1024 / 1024) . 'M';             
            die("{'오류':'업로드할 수 있는 용량을 초과했습니다. 최대 파일용량은 $size 입니다.'}");    
        }        
    }
    
    private function toBytes($str){
        $val = trim($str);
        $last = strtolower($str[strlen($str)-1]);
        switch($last) {
            case 'g': $val *= 1024;
            case 'm': $val *= 1024;
            case 'k': $val *= 1024;        
        }
        return $val;
    }
    
    /**
     * Returns array('success'=>true) or array('error'=>'error message')
     */
    function handleUpload($uploadDirectory, $baseDirectory, $replaceOldFile = FALSE){
        if (!is_writable($uploadDirectory)){
            return array('error' => "서버의 오류때문에 업로드하지 못했습니다.");
        }
        
        if (!$this->file){
            return array('error' => '업로드할 파일이 없습니다.');
        }
        
        $size = $this->file->getSize();
        
        if ($size == 0) {
            return array('error' => '파일이 비어있습니다.');
        }
        
        if ($size > $this->sizeLimit) {
            return array('error' => '파일용량이 너무 큽니다.');
        }
        
        $pathinfo = pathinfo($this->file->getName());

        if(!isset($pathinfo['extension'])) {
            $these = implode(', ', $this->allowedExtensions);
            return array('error' => '잘못된 파일 형식입니다, 업로드하실 수 있는 파일형식은 ('. $these . ') 입니다.');
		}
		
        $ext = $pathinfo['extension'];
      // 	$filename = str_replace('.'.$ext, '', $pathinfo['basename']);
        $filename = md5(uniqid());
       	
        if($this->allowedExtensions && !in_array(strtolower($ext), $this->allowedExtensions)){
            $these = implode(', ', $this->allowedExtensions);
            return array('error' => '잘못된 파일 형식입니다, 업로드하실 수 있는 파일형식은 ('. $these . ') 입니다.');
        }
        
        if(!$replaceOldFile){
            /// don't overwrite previous files that were uploaded
            while (file_exists($uploadDirectory . $filename . '.' . $ext)) {
                $filename .= rand(10, 99);
            }
        }
        
        if ($this->file->save($uploadDirectory . $filename . '.' . $ext)){
            return array('success'=>true, 'filename'=>site_url($baseDirectory . $filename.'.'.$ext), 'filevalue'=>$filename.'.'.$ext);
        } else {
            return array('success'=>false, 'error'=> '파일을 업로드하지 못했습니다.' . '업로드를 취소하였거나, 서버의 오류때문입니다.');
        }
        
    }    
}
?>