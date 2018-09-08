<?php
namespace app\services;
use app\foundation\Service;
use app\foundation\MulUpload;

class MulFileUploadService extends Service
{
    public function upload($uid, $module,$file_size)
    {
        $user['type'] = array_keys($_FILES);
        $relativePath = 'uploads/' . $module . '/' . date('Y-m-d', time());
        $uploadPath = \Yii::getAlias('@web/') . $relativePath;
        
        for ($i = 0;$i< $file_size;$i++){

            $upload = new MulUpload($_FILES['name' . $i], $uploadPath);
            $img = $upload->uploadFile();
            if ($img == 'ok') {
               $ret[$i] =   $relativePath . '/' . $upload->getFilename() . ':' .$_FILES['name'. $i]['size'].':'.$_FILES['name'. $i]['name'];
            } else {
                $this->setError('图片上传失败:' . $upload->getError());
                return false;
            }
        }
        return $ret;
    }
}
