<?php
namespace official\api\register;

use app\foundation\Api;
use app\services\CompanyRegisterService;

class ShowCompanyInfoApi extends Api
{
	public function run()
	{
		$companyId = \Yii::$app->request->post('companyId');
		$service = CompanyRegisterService::instance();
		$result = $service->showCompanyInfo($companyId);
		if($result === false)
        {
            return $this->logicError($service->error);
        }
		return ['msg' => $result];
	}
}