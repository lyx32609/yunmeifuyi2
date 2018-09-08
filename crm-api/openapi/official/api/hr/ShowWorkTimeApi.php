<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/8/10
 * Time: 15:37
 */

namespace official\api\hr;


use app\foundation\Api;
use Yii;
use app\services\ShowWorkTimeService;

class ShowWorkTimeApi extends Api
{
    public function run()
    {
        $company_id = trim(Yii::$app->request->post('company_id'));
        $page = trim(Yii::$app->request->post('page'));
        $pageCount = trim(Yii::$app->request->post('pageCount'));
        $service = ShowWorkTimeService::instance();
        $result = $service->showWorkTime($company_id,$page,$pageCount);
        if($result === false){
            return $this->logicError( $service->error, $service->errors);
        }

        return ['msg'=>$result];


    }

}