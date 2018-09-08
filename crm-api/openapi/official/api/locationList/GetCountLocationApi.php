<?php
namespace official\api\LocationList;

use app\foundation\Api;
use app\services\GetCountLocationService;

/**
 * 获取员工实时定位接口
 * @return array
 * @author lzk
 */
class GetCountLocationApi extends Api
{
    public function run()
    {
        $area = \Yii::$app->request->post('area');
        $city = \Yii::$app->request->post('city');
        $department = \Yii::$app->request->post('department');
        $startTime = \Yii::$app->request->post('startTime');
        $endTime = \Yii::$app->request->post('endTime');
        $service = GetCountLocationService::instance();
        $data = $service->getCountLocation($area, $city, $department, $startTime, $endTime);
        if($data === false)
        {
            return $this->logicError($service->error, $service->errors);
        }
        return ['msg'=>$data];
    }
}