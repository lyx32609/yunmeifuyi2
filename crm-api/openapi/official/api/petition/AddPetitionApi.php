<?php
namespace official\api\petition;

use app\foundation\Api;
use app\services\AddPetitionService;
use app\services\MulFileUploadService;
use app\services\FileUploadService;

/**
 * 提报签呈接口
 */
class AddPetitionApi extends Api
{
	public function run()
	{
	    //签呈信息
		$user_id = \Yii::$app->request->post('user_id');
		$title = \Yii::$app->request->post('title');
		$content = \Yii::$app->request->post('content');

        //审批人ids
        $ids = \Yii::$app->request->post('ids');
		//图片信息： 原图,原图
        $master_img = \Yii::$app->request->post('master_img');
		//附件信息：文件,文件
        $file = \Yii::$app->request->post('file');

		$service = AddPetitionService::instance();
		$result = $service->addPetition($user_id, $title,$content,$master_img,$file,$ids);
		if($result === false)
        {
            return $this->logicError($service->error);
        }
		return ['msg'=>$result];
	}
}
