<?php
namespace official\api\petition;

use app\foundation\Api;
use app\services\AddPetitionService;
use app\services\MulFileUploadService;
use app\services\FileUploadService;

/**
 * 提报签呈接口
 */
class AddPetitionNewApi extends Api
{
    public function run()
    {
        //提报人id
        $user_id = \Yii::$app->request->post('user_id');
        //审批人ids
        $ids = \Yii::$app->request->post('ids');
        //图片信息： 原图,原图
        $master_img = \Yii::$app->request->post('master_img');
        //附件信息：文件,文件
        $file = \Yii::$app->request->post('file');
        //签呈类型
        $type = \Yii::$app->request->post('type');
        //签呈信息
        $message = \Yii::$app->request->post('message');

        $service = AddPetitionService::instance();
        $result = $service->addPetitionNew($user_id,$master_img,$file,$ids,$type,$message);
        if($result === false)
        {
            return $this->logicError($service->error);
        }
        return ['msg'=>$result];
    }
}
