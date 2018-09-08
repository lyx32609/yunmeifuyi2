<?php
namespace official\api\LocationList;

use app\foundation\Api;
use app\services\LocationListService;

/**
 * 员工定位接口
 * @return array 
 * @author lzk
 */
class LocationListApi extends Api
{
    public function run()
    {
        $staffId = \Yii::$app->request->post('staffId');
        $start = \Yii::$app->request->post('start');
        $end = \Yii::$app->request->post('end');
        $page = \Yii::$app->request->post('page');
        $service = LocationListService::instance();
        $data = $service->getLocationList($staffId,$start,$end,$page);
        if($data === false)
        {
             return $this->logicError($service->error, $service->errors);
        }
        return ['locationList'=>$data];; 
    }
}