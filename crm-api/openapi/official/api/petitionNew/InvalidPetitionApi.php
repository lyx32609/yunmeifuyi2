<?php
namespace official\api\petitionNew;

use app\foundation\Api;
use app\services\PetitionNewTwoService;

/**
 * Class InquireApproverApi
 * 查询审批人
 * @package official\api\petitionNew
 */
class InvalidPetitionApi extends Api
{
    public function run()
    {
        $id = \Yii::$app->request->post('id');
        $invalid_description = \Yii::$app->request->post('invalid_description');//新加作废描述（必填）
        $service = PetitionNewTwoService::instance();
        $result = $service->InvalidPetition($id,$invalid_description);
        if($result === false)
        {
            return $this->logicError($service->error);
        }
        return ['msg'=>$result];
    }
}