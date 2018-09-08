<?php
namespace app\services;

use app\foundation\Service;
use app\models\User;
use app\models\UserDepartment;
use app\models\AuthAssignment;
class GetUserListSixService extends Service
{
    /**
     * 获取部门及人员信息列表
     */
    public function getUserList($area_id, $city_id, $company_id, $department_id)
    {
        if(!$area_id)
        {
            $this->setError('省id不能为空');
            return false;
        }
        if(!$city_id)
        {
            $this->setError('市id不能为空');
            return false;
        }
        if(!$company_id)
        {
            $this->setError('公司id不能为空');
            return false;
        }
        if(!$department_id)
        {
            $this->setError('部门id不能为空');
            return false;
        }
        $result = User::find()
                    ->select(['id', 'name', 'is_staff'])
                    ->where(['domain_id' => $city_id])
                    ->andWhere(['company_categroy_id' => $company_id])
                    ->andWhere(['department_id' => $department_id])
                    ->andFilterWhere(['is_staff'=>1])
                    ->asArray()
                    ->all();
        for($i = 0; $i < count($result); $i++)
        {
            $list[$i]['item_name'] = $this->checkLogin($result[$i]['id']);
            if($list[$i]['item_name']){
                    $result[$i]['lever'] = 1;
                    $result[$i]['item_name'] = $list[$i]['item_name'];
            } else {
                    $result[$i]['lever'] = 1;
                    $result[$i]['item_name'] = $list[$i]['item_name'];
            }
        }
        if(!$result)
        {
            $this->setError('暂无人员信息');
            return false;
        }
        return $result;
    }
    /**
     * 判断用户是否已经离职
     * @param  [type] $user_id [用户id]
     * @return [type]          [description]
     */
    public function checkLogin($user_id)
    {
        $data = AuthAssignment::find()
        ->select(['item_name'])
        ->where(['user_id' => $user_id])
        ->asArray()
        ->one();
        if(!$data){
            return $data['item_name'] = '';
        }
        return $data['item_name'];
    }
}