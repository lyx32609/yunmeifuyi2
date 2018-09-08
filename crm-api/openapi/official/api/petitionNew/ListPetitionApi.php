<?php

namespace official\api\petitionNew;


use app\foundation\Api;
use app\services\PetitionLatestService;

/*
 * 签呈列表及查询接口
 * */
class ListPetitionApi extends Api
{
    public function run()
    {
        $status = \Yii::$app->request->post('status');
        $flage = \Yii::$app->request->post('flage');  //签呈状态
        $type = \Yii::$app->request->post('type');    //签呈类型
        $user_id = \Yii::$app->request->post('user_id');
        $page_size = \Yii::$app->request->post('page_size');
        $page_count = \Yii::$app->request->post('page_count');

        $service = PetitionLatestService::instance();
        $result = $service->managePetitionNew($status,$user_id, $page_size,$page_count,$flage,$type);

        if ($result === false) {
            return $this->logicError($service->error);
        }
        return ['msg'=>$result];
    }
}