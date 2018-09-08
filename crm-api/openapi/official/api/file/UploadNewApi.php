<?php
namespace official\api\file;

use Yii;
use app\foundation\Upload;
use app\foundation\Api;
use app\services\FileUploadService;

class UploadNewApi extends Api
{
    public function run()
    {
        $context = \Yii::$app->request->post('context');
        
        $service = FileUploadService::instance();
        $ret = $service->uploadNew('official', $context);
        
        if($ret === false)
        {
            return $this->logicError($service->error);
        }
        
        return ['img'=>$ret];
    }
}
