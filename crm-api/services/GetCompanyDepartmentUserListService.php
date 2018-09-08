<?php
namespace app\services;

use app\foundation\Service;
use app\models\User;
use app\models\UserDepartment;
use app\models\AuthAssignment;
class GetCompanyDepartmentUserListService extends Service
{
    /**
     * 获取部门及人员信息列表
     */
    public function getCompanyDepartmentUserLis($department_id, $company_categroy_id)
    {
        if(!$company_categroy_id)
        {
            $this->setError('公司id不能为空');
            return false;
        }
        if(!$department_id){
            $list = UserDepartment::find()
                    ->select(['id', 'name', 'company'])
                    ->where(['company' => $company_categroy_id])
                    ->andWhere(['is_show' => 1])
                    ->asArray()
                    ->all();
            if(!$list){
                $this->setError('暂无部门信息');
                return false;
            }
            if($company_categroy_id == '1'){
                for($i = 0; $i < count($list); $i++) {
                    if($list[$i]['name'] == '离职部'){
                        $list[$i] = [];
                    }
                    
                }
                $j = 0;
                for($i = 0; $i < count($list) + 1; $i++){
                    if($list[$i]['id']){
                        $result[$j] = $list[$i];
                        $j++;
                    }
                }
            } else {
                return $list;
            }
        } else {
            $result = User::find()
                    ->select(['id', 'name', 'is_staff'])
                    ->where(['company_categroy_id' => $company_categroy_id])
                    ->andWhere(['department_id' => $department_id])
                    ->andFilterWhere(['is_staff'=>1])
                    ->asArray()
                    ->all();
            for($i = 0; $i < count($result); $i++){
                $list[$i]['item_name'] = $this->checkLogin($result[$i]['id']);
                if($list[$i]['item_name']){
                    $result[$i]['lever'] = 1;
                    $result[$i]['item_name'] = $list[$i]['item_name'];
                } else {
                    $result[$i]['lever'] = 1;
                    $result[$i]['item_name'] = $list[$i]['item_name'];
                }
            }
            if(!$result){
                $this->setError('暂无人员信息');
                return false;
            }
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