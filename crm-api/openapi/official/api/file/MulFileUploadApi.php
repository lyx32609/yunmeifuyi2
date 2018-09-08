<?php
namespace official\api\file;

use Yii;
use app\foundation\Api;
use app\services\MulFileUploadService;

class MulFileUploadApi extends Api
{
    public function run()
    {
        $file_size = \Yii::$app->request->post('file_size');

        $service = MulFileUploadService::instance();
        $ret = $service->upload(Yii::$app->user->id, 'official',$file_size);
        if($ret === false)
        {
            return $this->logicError($service->error);
        }
        return ['img'=>$ret];
    }
}