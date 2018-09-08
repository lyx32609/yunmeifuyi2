<?php
namespace official\api\deliver;

use app\foundation\Api;
use app\services\WmsNewService;


class CarsNewApi extends Api
{
    public function run()
    {
        $user_id = \Yii::$app->request->post('user_id');
        $is_cooperation = \Yii::$app->request->post('is_cooperation');
        $company_category_id = \Yii::$app->request->post('company_category_id');
        $service = WmsNewService::instance();
        $ret = $service->getCars($user_id, $is_cooperation, $company_category_id);
        if($ret === false)
        {
            return $this->logicError($service->error);
        }
        if(isset($ret['ret']) && $ret['ret'] == 10)
        {
            return $ret;
        }
        if(isset($ret['ret']) && $ret['ret'] == 2)
        {
            return $ret;
        }
        if(isset($ret['ret']) && $ret['ret'] == 28)
        {
            return $ret;
        }
        return [
            'result' => $ret,
        ];
        
    }
}