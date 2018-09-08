<?php
namespace app\services;
use app\foundation\Service;
use app\foundation\Upload;

class FileUploadService extends Service
{
    public function upload($uid, $module, $context = '')
    {
        
     
        
        $user['type'] = array_keys($_FILES);
        $relativePath = 'uploads/'.$module.'/'.date('Y-m-d',time());
        $uploadPath = \Yii::getAlias('@web/') . $relativePath;

        $upload = new Upload(array_keys($_FILES)[0], $uploadPath);
        $img = $upload->uploadFile();
        if($img)
        {
            return $relativePath.'/'.$upload->getFilename();
        }
        else
        {
            $this->setError('图片上传失败:'.$upload->getError());
            return false;
        }
    }
    /**
     * 企业注册时调用
     * @param  [type] $uid     [description]
     * @param  [type] $module  [description]
     * @param  string $context [description]
     * @return [type]          [description]
     */
    public function uploadNew($module, $context = '')
    {
        $user['type'] = array_keys($_FILES);
        $relativePath = 'uploads/'.$module.'/'.date('Y-m-d',time());
        $uploadPath = \Yii::getAlias('@web/') . $relativePath;
        $upload = new Upload(array_keys($_FILES)[0], $uploadPath);
        $img = $upload->uploadFile();
        if($img)
        {
            return $relativePath.'/'.$upload->getFilename();
        }
        else
        {
            $this->setError('图片上传失败:'.$upload->getError());
            return false;
        }
    }
}