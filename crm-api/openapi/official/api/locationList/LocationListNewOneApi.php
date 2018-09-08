<?php
namespace official\api\LocationList;

use app\foundation\Api;
use app\services\LocationListNewOneService;

/**
 * 员工定位接口
 * @return array
 * @author lzk
 */
class LocationListNewOneApi extends Api
{
    public function run()
    {
        $staffId = \Yii::$app->request->post('staffId');
        $start = \Yii::$app->request->post('start');
        $end = \Yii::$app->request->post('end');
        $offset = \Yii::$app->request->post('offset');
        $limit= \Yii::$app->request->post('limit');
        $type = \Yii::$app->request->post('type');
        $service = LocationListNewOneService::instance();
        $data = $service->getLocationList($staffId,$start,$end,$offset,$limit,$type);
        if($data === false)
        {
            return $this->logicError($service->error, $service->errors);
        }
        return ['locationList'=>$data];
    }
}