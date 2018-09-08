<?php
namespace official\api\petitionNew;

use app\foundation\Api;
use app\services\DetailPetitionNewService;

/**
 * Class DetailPetitionReceiveApi
 * 接收签呈详情接口
 * @package official\api\petitionNew
 */
class DetailPetitionReceiveApi extends Api
{
    public function run()
    {
        $petition_id = \Yii::$app->request->post('petition_id');
        $user_id = \Yii::$app->request->post('user_id');
        $service = DetailPetitionNewService::instance();
        $result = $service->detailPetitionReceive($petition_id,$user_id);

        if($result === false)
        {
            return $this->logicError($service->error);
        }
        return ['msg'=>$result];
    }
}