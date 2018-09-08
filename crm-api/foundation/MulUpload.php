<?php
namespace app\foundation;
class MulUpload{
    protected $fileName;
    protected $maxSize;
    protected $allowMime;
    protected $allowExt;
    protected $uploadPath;
    protected $imgFlag;
    protected $fileInfo;
    protected $error;
    protected $ext;
    /**
     * @param string $fileName
     * @param string $uploadPath
     * @param string $imgFlag
     * @param number $maxSize
     * @param array $allowExt
     * @param array $allowMime
     */
    public function __construct($fileInfo,$uploadPath='./uploads',$imgFlag=true,$maxSize=8388608,$allowExt=array('jpeg','jpg','png','pdf','xls','txt','doc','docx','xlsx','ppt','pptx','log','dot','pps','pot','xlt','dotx','ppsx','csv','xltx'),$allowMime=array('image/jpg','image/jpeg','image/png','application/pdf','application/vnd.ms-excel','text/plain','application/msword','application/vnd.openxmlformats-officedocument.wordprocessingml.document','application/vnd.openxmlformats-officedocument.spreadsheetml.sheet','application/vnd.ms-powerpoint','application/vnd.openxmlformats-officedocument.presentationml.presentation','application/msword','application/vnd.openxmlformats-officedocument.wordprocessingml.template','application/vnd.openxmlformats-officedocument.presentationml.slideshow','application/octet-stream','application/vnd.openxmlformats-officedocument.spreadsheetml.template')){
        $this->maxSize=$maxSize;
        $this->allowMime=$allowMime;
        $this->allowExt=$allowExt;
        $this->uploadPath=$uploadPath;
        $this->imgFlag=$imgFlag;
        $this->fileInfo=$fileInfo;
    }
    /**
     * 检测上传文件是否出错
     * @return boolean
     */
    protected function checkError(){
        if(!is_null($this->fileInfo)){
            if($this->fileInfo['error']>0){
                switch($this->fileInfo['error']){
                    case 1:
                        $this->error='上传文件过大';//超过了PHP配置文件中upload_max_filesize选项的值
                        break;
                    case 2:
                        $this->error='上传文件过大';//超过了表单中MAX_FILE_SIZE设置的值
                        break;
                    case 3:
                        $this->error='文件部分被上传';
                        break;
                    case 4:
                        $this->error='没有选择上传文件';
                        break;
                    case 6:
                        $this->error='目录不存在';//没有找到临时目录
                        break;
                    case 7:
                        $this->error='文件不可写';
                        break;
                    case 8:
                        $this->error='文件上传中断';//由于PHP的扩展程序中断文件上传
                        break;

                }
                return false;
            }else{
                return true;
            }
        }else{
            $this->error='文件上传出错';
            return false;
        }
    }
    /**
     * 检测上传文件的大小
     * @return boolean
     */
    protected function checkSize(){
        if($this->fileInfo['size']>$this->maxSize){
            $this->error='上传文件过大';
            return false;
        }
        return true;
    }
    /**
     * 检测扩展名
     * @return boolean
     */
    protected function checkExt(){
        $this->ext=strtolower(pathinfo($this->fileInfo['name'],PATHINFO_EXTENSION));
        if(!in_array($this->ext,$this->allowExt)){
            $this->error='文件类型不正确';//不允许的扩展名
            return false;
        }
        return true;
    }
    /**
     * 检测文件的类型
     * @return boolean
     */
    protected function checkMime(){
        if(!in_array($this->fileInfo['type'],$this->allowMime)){
            $this->error='文件类型不正确';//不允许的文件类型
            return false;
        }
        return true;
    }
    /**
     * 检测是否是真实图片
     * @return boolean
     */
    protected function checkTrueImg(){
        if($this->imgFlag){
            if(!@getimagesize($this->fileInfo['tmp_name'])){
                $this->error='不是真实图片';
                return false;
            }
            return true;
        }
    }
    /**
     * 检测是否通过HTTP POST方式上传上来的
     * @return boolean
     */
    protected function checkHTTPPost(){
        if(!is_uploaded_file($this->fileInfo['tmp_name'])){
            $this->error='文件不是通过HTTP POST方式上传上来的';
            return false;
        }
        return true;
    }
    /**
     *显示错误
     */
    protected function showError(){
        echo('<span style="color:red">'.$this->error.'</span>');
    }
    /**
     * 检测目录不存在则创建
     */
    protected function checkUploadPath(){
        if(!file_exists($this->uploadPath)){
            mkdir($this->uploadPath,0777,true);
        }
    }
    /**
     * 产生唯一字符串
     * @return string
     */
    protected function getUniName(){
        return md5(uniqid(microtime(true),true));
    }
    /**
     * 上传文件
     * @return string
     */
    public function uploadFile(){
        if($this->checkError()&&$this->checkSize()&&$this->checkExt()&&$this->checkMime()&&$this->checkHTTPPost()){
            $this->checkUploadPath();
            $this->uniName=$this->getUniName();
            $this->destination=$this->uploadPath.'/'.$this->uniName.'.'.$this->ext;
            if(@move_uploaded_file($this->fileInfo['tmp_name'], $this->destination)){
                return 'ok';
            }else{
                $this->error='文件上传失败！';//文件移动失败
                return ['ret'=>100,'msg'=>$this->getError()];exit;
//				return false;
// 				$this->showError();
            }
        }else{
//		    $this->getError();
            return ['ret'=>100,'msg'=>$this->getError()];exit;
//            file_put_contents("d:/Y9.log",print_r($this->getError(),true),FILE_APPEND);exit;
//		    return false;
// 			$this->showError();
        }
    }

    public function getError()
    {
        return $this->error;
    }

    public function getFilename()
    {
        return $this->uniName.'.'.$this->ext;
    }
}



