<?php
namespace official\api\file;

use Yii;
use app\foundation\Upload;
use app\foundation\Api;
use app\services\FileUploadService;

class UploadApi extends Api
{
    public function run()
    {
        $context = \Yii::$app->request->post('context');
        
        $service = FileUploadService::instance();
        $ret = $service->upload(Yii::$app->user->id, 'official', $context);
        
        if($ret === false)
        {
            return $this->logicError($service->error);
        }
        
        return ['img'=>$ret];
    }
}
