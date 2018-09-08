<?php
namespace official\api\business;
use app\foundation\Api;
use app\services\GetBusinessRankNoPageService;
/**
 * Created by 付腊梅.
 * User: Administrator
 * Date: 2017/3/7 0007
 * Time: 上午 8:54
 */


class GetBusinessRankNoPageApi extends Api
{
    public function run()
    {
        $area = \Yii::$app->request->post('area');
        $city = \Yii::$app->request->post('city');
        $department = \Yii::$app->request->post('department');
        $group = \Yii::$app->request->post('group');
        $timeType = \Yii::$app->request->post("timeType");
        if($timeType == '1')//昨日
        {
            // $stime = mktime(0,0,0,date('m'),date('d')-1,date('Y'));//本日开始
            // $etime = mktime(23,59,59,date('m'),date('d'),date('Y')-1);//本日结束
            $stime = mktime(0,0,0,date('m'),date('d')-1,date('Y'));//昨日开始
            $etime = mktime(23,59,59,date('m'),date('d')-1,date('Y'));//昨日结束
        }
        elseif($timeType == '2')
        {
            // $stime = mktime(0,0,0,date('m'),date('d')-date('w'),date('Y'));//本周开始
            // $etime = mktime(23,59,59,date('m'),date('d')-date('w')+6,date('Y'));//本周结束
            $stime = mktime(0,0,0,date('m'),date('d')-date('w'),date('Y'));//本周开始
            $etime = mktime(23,59,59,date('m'),date('d')-1,date('Y'));//本周结束（昨天晚上）
        }
        elseif($timeType == '3')
        {
            // $stime = mktime(0,0,0,date('m'),1,date('Y'));//本月开始
            // $etime = mktime(23,59,59,date('m'),date('t'),date('Y'));//本月结束
            $stime = mktime(0,0,0,date('m'),1,date('Y'));//本月开始
            $etime = mktime(23,59,59,date('m'),date('d')-1,date('Y'));//本月结束（昨天晚上）/////
        }
        $type = \Yii::$app->request->post('type');
        $page = \Yii::$app->request->post('page');
        $pageSize = \Yii::$app->request->post('pageSize');
        $service = GetBusinessRankNoPageService::instance();
        $result =$service->getBusinessRank($area,$city,$department,$stime,$etime,$type,$group);//
        if($result === false)
        {
            return $this->logicError($service->error);
        }
        return ['msg' => $result];
    }
}