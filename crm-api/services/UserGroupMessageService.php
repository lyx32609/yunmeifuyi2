<?php
namespace app\services;

use app\foundation\Service;
use app\models\UserGroup;
use app\models\User;
use app\models\UserDepartment;
use app\models\UserDomain; 
use app\models\CompanyCategroy; 
use app\models\UserBusinessNotes; 
use app\models\ShopNote; 
use frontend;
class UserGroupMessageService extends Service
{
    /* 
     * @parms int $domain_id
     *  根据区域获取区域内的分组
     *  */
    public function getGroups($domain_id)
    {
        if(!$domain_id)
        {
            $this->setError('区域ID不可为空');
            return false;
        }
        $crm_domain = UserDomain::findOne(['domain_id' => $domain_id]);
        $company = $this->getCompany();
        $department = $this->getDepartment($company);
        $groups = UserGroup::find()
                ->select(['id', 'name', 'desc'])
                ->andWhere('domain_id = '.$crm_domain->are_region_id)
                ->andWhere(['in', 'department_id', $department])
                ->andWhere(['is_select' => 1])
                ->orderBy('priority desc')
                ->asArray()
                ->all();
        if(!$groups)
        {
            $this->setError('获取分组信息失败');
            return false;
        }
        return ['msg' => $groups];
    }
    /* 
     * @parms int $group_id
     * 获取分组成员id
     *  */
    public function getGroupUsers($group_id)
    {
        if(!$group_id)
        {
            $this->setError('分组信息错误');
            return false;
        }
        $users = User::find()
                ->select('username')
                ->andWhere('group_id=:group_id',[':group_id' => $group_id])
                ->andWhere(['is_select' => 1])
                ->column();
        for($i = 0; $i < count($users); $i++){
             $users[$i]['domain_id'] = $this->getDomainId($users[$i]['domain_id']);
        }
        if(!$users)
        {
            $this->setError('获取组内成员信息失败');
            return false;
        }
        return ['msg' => $users];
    }
    
    /* 
     * 获取相关区域 相关部门人员信息
     * @params inter $domain_id   $department_id 
     *  */
    public function getDomainStaff($domain_id, $limit, $offset)
    {
        if(!$domain_id)
        {
            $this->setError('参数信息不可空');
            return false;
        }
        $crm_domain = UserDomain::findOne(['domain_id' => $domain_id]);
        $company = $this->getCompany();
        $users = User::find()
                 ->select(User::tableName().'.id,username,'.User::tableName().'.name,phone,group_id,'. User::tableName() .'.domain_id,u.name as group_name,u.department_id')
                ->andWhere(User::tableName().'.domain_id=:domain_id',[':domain_id'=>$crm_domain->are_region_id])
                ->andWhere(['in', User::tableName().'.company_categroy_id', $company])
                ->leftJoin(UserGroup::tableName().' u', User::tableName().'.group_id = u.id') 
                ->andWhere([User::tableName().'.is_select' => 1]);
        if($limit || $offset) {
            
                $users->limit($limit);
                $users->offset($offset);
        }
               
                $users->asArray();
                $model=$users->all();
        
        if(!$model)
        {
            $this->setError('获取用户失败');
            return false;
        }
        for($i = 0; $i < count($model); $i++){
            $model[$i]['domain_id'] = $this->getDomainId($model[$i]['domain_id']);
        }
        return $model;
    }
    
    
    public function getDomainStaffCount($domain_id)
    {
        if(!$domain_id)
        {
            $this->setError('参数信息不可空');
            return false;
        }
        $crm_domain = UserDomain::findOne(['domain_id' => $domain_id]);
        $company = $this->getCompany();
        $department = $this->getDepartment($company);
        $users=User::find()
                ->andWhere('domain_id=:domain_id',[':domain_id' => $crm_domain->are_region_id])
                ->andWhere(['in', 'department_id', $department])
                ->andWhere('is_select = 1')
                ->asArray()
                ->all();
        for($i = 0; $i < count($users); $i++){
            $users[$i]['domain_id'] = $this->getDomainId($users[$i]['domain_id']);
        }
        if(!$users)
        {
            $this->setError('获取用户失败');
            return false;
        }
        return $users;
    }
    
    /* 
     * 获取区域内部门
     *  */
    public function getDepartments($domain_id)
    {
        
        if(!$domain_id)
        {
            $this->setError('参数信息不可空');
            return false;
        }
        $crm_domain = UserDomain::findOne(['domain_id' => $domain_id]);
        $company = $this->getCompany();
        $departments=UserDepartment::find()
                ->select(['id', 'name'])
                ->andwhere('domain_id=:domain_id',[':domain_id' => $crm_domain->are_region_id])
                ->andWhere(['in', 'company_id', $company])
                ->andWhere(['is_select' => 1])
                ->andWhere(['is_show' => 1])
                ->orderBy('priority desc')
                ->asArray()
                ->all();
        
        if(!$departments)
        {
            $this->setError('获取部门失败');
            return false;
        }
        return ['msg' => $departments];
    }
    /**
     * 获取地区部门列表
     * @param unknown $domain
     */
    public function getDomainDepartmentList($domain)
    {
        $company = $this->getCompany();
        $department = $this->getDepartment($company, $domain);
        if(!$department){
            $this->setError('暂无部门信息');
            return false;
        }
        return $department;
    }
    /**
     * 业务记录拜访商家查询
     * @param unknown $user
     * @param unknown $start
     * @param unknown $end
     * @return number
     */
    public function getUserLocationNum($user, $start, $end)
    {
        $user_list = json_decode($user);
        $result = $this->visitData($user_list, $start, $end);
        return $result;
    }
    /**
     * 业务记录拜访商家查询
     * @param unknown $user
     * @param unknown $start
     * @param unknown $end
     * @return number
     */
    private function visitData($user, $start, $end)
    {
        if(is_array($user)){
            $user = implode(',', $user);
        }
        /* 西门中凯  初级写法 */
        $rows = (new \yii\db\Query())
                ->select('count(id) as visitNum')
                ->from(ShopNote::tableName())
                ->andWhere('user in ('.$user.')')
                ->andWhere('time >= :start and time < :end', [':start'=>$start, ':end'=>$end])
                ->one(\Yii::$app->dbofficial);
        /* 西门中凯  升级后写法 */
        $nums = UserBusinessNotes::find()
            ->andWhere('staff_num in ('.$user.')')
            ->andWhere('time >= :start and time < :end', [':start'=>$start, ':end'=>$end])
            ->count();
        $row = $rows['visitNum']+$nums;
        return $row;
    }
    /**
     * 获取部门人员列表
     * @param unknown $department_id
     * @return boolean
     */
    public function getDepartmentUserList($department_id)
    {
        $result = $this->getUser($department_id);
        if(!$result){
            $this->setError('暂无人员信息');
            return false;
        }
        $list = [];
        foreach ($result as $v){
            $list[] = $v['username'];
        }
        return $list;
    }
    public function getUser($department_id)
    {
        $result = User::find()
                ->select(['username'])
                ->where(['department_id' => $department_id])
                ->andWhere(['is_select' => 1])
                ->asArray()
                ->all();
        if(!$result){
            return false;            
        }
        return $result;
    }
    /**
     * 获取组内人员列表
     * @param unknown $group_id
     * @return boolean|boolean|\yii\db\ActiveRecord[]
     */
    public function getGroupUserList($group_id)
    {
        $result = $this->getGroupUser($group_id);
        if(!$result){
            $this->setError('获取组内人员失败');
            return false;
        }
        $list = [];
        foreach ($result as $v){
            $list[] = $v['username'];
        }
        return $list;
    }
    private function getGroupUser($group_id)
    {
        $result = User::find()
                ->select(['username'])
                ->where(['group_id' => $group_id])
                ->andWhere(['is_select' => 1])
                ->asArray()
                ->all();
        if(!$result){
            return false;
        }
        return $result;
    }
    /**
     * 获取区域内部门id
     * @param unknown $domain_id
     */
    public function getDomainGroupList($domain_id)
    {
        $company = $this->getCompany();
        $department = $this->getDepartment($company, $domain_id);
        if(!$department){
            $this->setError('该地区暂无部门');
            return false;
        }
        foreach ($department as $v){
            $list[] = $v['id'];
        }
        $result = $this->getGroup($list);
        if(!$result){
            $this->setError('该地区暂无组信息');
            return false;
        }
        foreach ($result as $k => $v){
            $result[$k]['domain_id'] = $domain_id;
        }
        return $result;
    }
    /**
     * 获取组信息列表
     * @param unknown $department
     * @return boolean|\yii\db\ActiveRecord[]
     */
    public function getGroup($department)
    {
        $result = UserGroup::find()
                ->select(['id', 'name', 'desc', 'domain_id'])
                ->where(['in', 'department_id', $department])
                ->andWhere(['is_select' => 1])
                ->asArray()
                ->all();
        if(!$result){
            return false;
        }
        return  $result;
    }
    /**
     * 获取各城市运营商id
     * @return unknown[]
     */
    public function getCompany()
    {
        $result = CompanyCategroy::find()
                ->select('id')
                ->where(['fly' => 1])
                ->asArray()
                ->all();
        $list = [];
        for($i = 0; $i < count($result); $i++){
            $list[$i] = $result[$i]['id'];
        }
        array_unshift($list, 1);
        return $list;
    }
    /**
     * 获取部门
     * @param unknown $company
     */
    public function getDepartment($company, $domain = null)
    {
        if(!$domain){
            $result = UserDepartment::find()
                   ->select(['id'])
                   ->where(['in', 'company', $company])
                   ->andWhere(['is_select' => 1])
                   ->andWhere(['is_show' => 1])
                   ->asArray()
                   ->all();
            $list = [];
            for($i = 0; $i < count($result); $i++){
                $list[$i] = $result[$i]['id'];
            }
        } else {
            $domain_id = UserDomain::findOne(['domain_id' => $domain]);
            if(!$domain_id){
                $this->setError('地区不存在');
                return false;
            }
            $result = UserDepartment::find()
                    ->select(['id', 'name', 'domain_id'])
                    ->where(['in', 'company', $company])
                    ->andWhere(['domain_id' => $domain_id->are_region_id])
                    ->andWhere(['is_select' => 1])
                    ->andWhere(['is_show' => 1])
                    ->asArray()
                    ->all();
            if(!$result){
                return false;
            }
            foreach ($result as $v){
                $v['domain_id'] = $domain;
            }
            return $result;
        }
        
        return $list;
    }
    public function getDomainId($domian)
    {
        $result = UserDomain::find()
                ->select(['domain_id'])
                ->where(['are_region_id' => $domian])
                ->asArray()
                ->all();
        if(!$result){
            return false;
        }
        return $result[0]['domain_id'];
    }
}