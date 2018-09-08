<?php
namespace official\api\neighbors;

use app\foundation\Api;
use app\services\NeighborsNewService;

/**
 * 获取周围供应商列表接口
 */
class ShowAroundSupplierNewApi extends Api
{
    public function run()
    {
        $userId = \Yii::$app->user->id;
        $longitude = \Yii::$app->request->post('longitude');
        $latitude = \Yii::$app->request->post('latitude');
        $range = \Yii::$app->request->post('range');
        $range = $range? intval($range):5;                             //默认为 5km
        $page = \Yii::$app->request->post('page');
        $page = $page? intval($page):1;
        $per_page = \Yii::$app->request->post('per_page');
        $per_page = intval($per_page);                   //默认每页显示10条记录
        $is_cooperation = \Yii::$app->request->post('is_cooperation');
        $company_category_id = \Yii::$app->request->post('company_category_id');
        $car_id = \Yii::$app->request->post('car_id');
        
        $service = NeighborsNewService::instance();
        
        $data = $service->getAroundSupplierList([
            'uid' => $userId,
            'longitude' => $longitude,
            'latitude' => $latitude,
            'range' => $range,
            'page' => $page,
            'per_page' => $per_page,
        ], $is_cooperation, $company_category_id, $car_id);
        if($data===false)
        {
            return $this->logicError($service->error, $service->errors);
        }
        
        $result = [
            'page' => $page,                         //当前页
            'totalNum' => $data['totalNum'],         //记录总数
            'totalPage' => $data['totalPage'],       //总页数
            'list' => $data['minfo'],                //数据列表
        ];
        
        return $result;
    }
}