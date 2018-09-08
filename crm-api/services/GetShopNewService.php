<?php
namespace app\services;

use app\foundation\Service;
use app\models\Shop;
use app\models\UserBusiness;
use app\models\UserDomain;
class GetShopNewService extends Service
{
    /**
     * 查询店铺是否存在（改版后）
     * @param unknown $user_id
     * @param unknown $is_cooperation
     * @param unknown $company_category_id
     * @param unknown $shop_name
     * @return boolean|string
     */
    public function getShopNew($user_id, $is_cooperation, $company_category_id, $shop_name)
    {
        if(!$user_id){
            $this->setError('用户不能为空');
            return false;
        }
        if(!$company_category_id){
            $this->setError('公司不能为空');
            return false;
        }
        if(!$shop_name){
            $this->setError('店铺名称不能为空');
            return false;
        }
        $user = User::find(['id' => $user_id]);
        if(!$user){
            $this->setError('用户不存在');
            return false;
        }
        if($is_cooperation == 0){
            $result = Shop::findOne(['shop_name' => $shop_name, 'company_category_id' => $company_category_id]);
        } else {
            $domain = UserDomain::findOne(['are_region_id' => $user->domain_id]);
            $result = UserBusiness::findOne(['customer_name' => $shop_name, 'domain_id' => $domain->id]);
        }
        if($result){
            $this->setError('店铺已存在');
            return false;
        }
        return $result = '可以添加';
    }
}