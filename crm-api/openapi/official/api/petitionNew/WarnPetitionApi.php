<?php
namespace official\api\petitionNew;

use app\foundation\Api;
use app\services\PetitionNewTwoService;

/**
 * 签呈审核提醒推送接口
 */
class WarnPetitionApi extends Api
{
    public function run()
    {
        $petition_id = \Yii::$app->request->post('petition_id');

        $service = PetitionNewTwoService::instance();
        $result = $service->warnPetition($petition_id);
        if($result === false)
        {
            return $this->logicError($service->error);
        }
        return ['msg'=>$result];
    }
}
