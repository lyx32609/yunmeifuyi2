<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/11/24
 * Time: 11:18
 */
namespace app\services;

use app\foundation\PinYin;
use app\foundation\Service;

use app\models\Regions;

use yii;
class AdService extends Service
{
    /**
     * @param $city
     * @return mixed
     */
    public function getAd($city)
    {
        $city = Regions::find()
            ->select('local_name')
            ->where(['region_id'=>$city])
            ->asArray()
            ->one();
        //汉字转换拼音
        $pinyin = new PinYin();
        $out=$pinyin->output($city['local_name']);
        /**
         * 广告接口调用集采广告系统  api/getad
         * position     广告位id
         * area         城市      格式jinanshi
         * resolution   分辨率
         */
        $result = Yii::$app->ad_api
        ->ad_request('api/getad',['position' => '6', 'area'=>$out ,'resolution'=>1]);
        if ($result['ret'] == 0){
            $res[] = $result['data'];
            return  $res;
        }else{
            $this->setError($result['msg']);
            return false;
        }
    }
}