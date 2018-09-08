<?php
namespace official\api\petitionNew;

use app\foundation\Api;
use app\services\DetailPetitionNewService;

/**
 * Class DetailPetitionApi
 * 发起的签呈详情接口
 * @package official\api\petitionNew
 */
class DetailPetitionApi extends Api
{
    public function run()
    {
        $petition_id = \Yii::$app->request->post('petition_id');
        $user_id = \Yii::$app->request->post('user_id');

        $service = DetailPetitionNewService::instance();
        $result = $service->detailPetition($petition_id,$user_id);

        if($result === false)
        {
            return $this->logicError($service->error);
        }
        return ['msg'=>$result];
    }
}