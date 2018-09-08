<?php
/**
 * Created by 付腊梅.
 * User: Administrator
 * Date: 2017/3/28 0028
 * Time: 下午 3:05
 */
namespace official\api\LocationList;

use app\foundation\Api;
use app\services\GetBusinessLocationByTypeService;

/**
 * 根据类型获取员工定位接口
 */
class GetBusinessLocationByTypeApi extends Api
{
    public function run()
    {
        $staffId = \Yii::$app->request->post('staffId');
        $start = \Yii::$app->request->post('start');
        $end = \Yii::$app->request->post('end');
        $offset = \Yii::$app->request->post('offset');
        $limit= \Yii::$app->request->post('limit');
        $type = \Yii::$app->request->post('type');
        $timeType = \Yii::$app->request->post('timeType');
        $service = GetBusinessLocationByTypeService::instance();
        $data = $service->getLocationList($staffId,$start,$end,$offset,$limit,$type,$timeType);
        if($data === false)
        {
            return $this->logicError($service->error, $service->errors);
        }
        return ['locationList'=>$data];
    }
}