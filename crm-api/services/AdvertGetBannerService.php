<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/1/30
 * Time: 11:27
 */

namespace app\services;

use app\foundation\Service;
use app\models\Banner;
use Yii;

class AdvertGetBannerService extends Service
{
    /**
     * 获取 合适的首页
     * @return bool
     */
    public function getBanner()
    {
          $banner =  Banner::find()->where(['id'=>18])->one();
            if (!$banner){
                $this->setError('未匹配到合适首页图片');
                return false;
            }
          $start_time = $banner['start_time'];
          $end_time = $banner['end_time'];

          if ($start_time < time() && time() < $end_time){
              $res['url'] = 'http://crm.xunmall.com' . $banner['images'];
              $res['version'] = $banner['is_valid'];

              return $res;
          }else{
              $this->setError('首页已过期');
              return false;
          }
    }


}