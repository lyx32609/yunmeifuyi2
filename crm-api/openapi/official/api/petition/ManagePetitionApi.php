<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/10/9
 * Time: 15:30
 */
namespace official\api\petition;


use app\foundation\Api;
use app\services\ListPetitionService;


class ManagePetitionApi extends Api
{
    public function run()
    {
        $status = \Yii::$app->request->post('status');
        $user_id = \Yii::$app->request->post('user_id');
        $page_size = \Yii::$app->request->post('page_size');
        $page_count = \Yii::$app->request->post('page_count');

        $service = ListPetitionService::instance();
        $result = $service->managePetition($status,$user_id, $page_size,$page_count);

        if ($result === false) {
            return $this->logicError($service->error);
        }
        return ['msg'=>$result];
    }
}