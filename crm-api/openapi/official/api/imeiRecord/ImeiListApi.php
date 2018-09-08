<?php
namespace official\api\imeiRecord;

use app\foundation\Api;
use app\services\ImeiService;

class ImeiListApi extends Api
{
    public function run()
    {
        $company_categroy_id = \Yii::$app->request->post('company_categroy_id');
        $page = \Yii::$app->request->post('page');
        $pageSize = \Yii::$app->request->post('pageSize');
        $start_time = \Yii::$app->request->post('start_time');
        $end_time = \Yii::$app->request->post('end_time');
        $service = ImeiService::instance();
        $result = $service->getImeiList($company_categroy_id,$page,$pageSize,$start_time,$end_time);
        if($result===false)
        {
            return $this->logicError($service->error, $service->errors);
        }
        return ['result'=>$result];

    }

}