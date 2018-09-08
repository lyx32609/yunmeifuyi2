<?php
namespace official\api\neighbors;

use app\foundation\Api;
use app\services\NeighborsService;

/**
 * 获取周围店铺列表接口
 */
class ShowAroundStoresApi extends Api
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
        $per_page = intval($per_page);                   //默认显示全部记录
        $flag=\Yii::$app->request->post('flag');
        $flag=$flag?$flag:0;
        $service = NeighborsService::instance();
        
        $data = $service->getAroundStoreList([
            'uid' => $userId,
            'longitude' => $longitude,
            'latitude' => $latitude,
            'range' => $range,
            'page' => $page,
            'per_page' => $per_page,
            'flag'=>$flag,
        ]);
        
        if($data===false)
        {
            return $this->logicError($service->error, $service->errors);
        }
        if($data['ret']==10)
        {
            return $data;
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