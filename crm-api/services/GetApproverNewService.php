<?php
namespace app\services;

use app\foundation\Service;
use app\models\AuthAssignment;
use app\models\CompanyCategroy;
use app\models\User;
use app\models\UserDepartment;
use official\Identity;
use app\models\AuthItemNum;

class GetApproverNewService extends Service
{
    /**
     * @param $name
     * @param $username
     * 查询审批人
     * @return array|bool
     */
    public function InquireApprover($name, $username,$user_id)
    {
        if(!$user_id) {
            $this->setError('用户ID不能为空');
            return false;
        }
        $people = User::find()
            ->andWhere('id = :user',[':user'=>$user_id])
            ->one();
        if(!$people) {
            $this->setError('员工不存在!');
            return false;
        }
        //判断是注册公司还是子公司
        $type = CompanyCategroy::find()
            ->select('fly')
            ->where(['id'=>$people->company_categroy_id])
            ->asArray()
            ->all();
        //子公司经理查询 是在总经办和 城市运营部和 实施部查询
        if ($people->rank == 3 && $people->company_categroy_id != 1 && $type[0]['fly'] == 1){
            return $this->subPeople($name, $username,$people,$user_id);
            //其余的都是在本公司的全部人中查询
        }else{
            return $this->findPeople($name, $username, $people,$user_id);
        }
    }
    /**
     * @param $name
     * @param $username
     * @param $people
     * @param $user_id
     * 查询 实施部城市运营部和总经办的人
     * @return array|bool|\yii\db\ActiveRecord[]
     */
    public function subPeople($name, $username,$people,$user_id)
    {
        $array = ['16','22','27']; //实施部 城市运营部和总经办的部门id
        //根据工号查询审批人
        if (empty($name) && !empty($username)){
            $result = User::find()
                ->leftJoin('off_user_department','off_user_department.id=off_user.department_id')
                ->leftJoin('auth_assignment', 'off_user.id=auth_assignment.user_id')
                ->select('off_user.id,off_user.name,off_user_department.name dname')
                ->andWhere(['off_user.username'=>$username])
                ->andWhere('off_user.is_staff = 1')
                ->andWhere(['<>','off_user.id',$user_id])
                ->andWhere(['in','off_user.department_id',$array])
                ->andWhere(['<>','auth_assignment.item_name','deliver'])
                ->asArray()
                ->all();
            if (empty($result)){
                $this->setError('未查询到相关信息');
                return false;
            }
            //根据姓名查询审批人
        }elseif (empty($username) && !empty($name)){
            $result = User::find()
                ->leftJoin('off_user_department','off_user_department.id=off_user.department_id')
                ->leftJoin('auth_assignment', 'off_user.id=auth_assignment.user_id')
                ->select('off_user.id,off_user.name,off_user_department.name dname')
                ->andWhere(['like','off_user.name',$name])
                ->andWhere('off_user.is_staff = 1')
                ->andWhere(['<>','off_user.id',$user_id])
                ->andWhere(['in','off_user.department_id',$array])
                ->andWhere(['<>','auth_assignment.item_name','deliver'])
                ->asArray()
                ->all();
            if (empty($result)){
                $this->setError('未查询到相关信息');
                return false;
            }
            //根据两个一块查询
        }elseif (!empty($username) && !empty($name)){
            $result = User::find()
                ->leftJoin('off_user_department','off_user_department.id=off_user.department_id')
                ->leftJoin('auth_assignment', 'off_user.id=auth_assignment.user_id')
                ->select('off_user.id,off_user.name,off_user_department.name dname')
                ->andWhere(['off_user.name'=>$name])
                ->andWhere(['off_user.username'=>$username])
                ->andWhere('off_user.is_staff = 1')
                ->andWhere(['<>','off_user.id',$user_id])
                ->andWhere(['in','off_user.department_id',$array])
                ->andWhere(['<>','auth_assignment.item_name','deliver'])
                ->asArray()
                ->all();
            if (empty($result)){
                $this->setError('未查询到相关信息');
                return false;
            }
        }elseif (empty($username) && empty($name)){
            $this->setError('请输入查询条件');
            return false;
        }
        //查询公司名
        $company = CompanyCategroy::find()->select('name')->where(['id'=>$people->company_categroy_id])->one();
        foreach ($result as $key => $value)
        {
            if (empty($result[$key]['dname'])){
                $result[$key]['dname'] = '';
            }else{
                $result[$key]['dname'] = $company->name . $result[$key]['dname'];
            }
        }
        return $result;
    }
    /**
     * @param $name
     * @param $username
     * @param $people
     * @param $user_id
     * 查询本公司的人
     * @return array|bool|\yii\db\ActiveRecord[]
     */
    public function findPeople($name, $username, $people,$user_id)
    {
        //根据工号查询审批人
        if (empty($name) && !empty($username)){
            $result = User::find()
                ->leftJoin('off_user_department','off_user_department.id=off_user.department_id')
                ->leftJoin('auth_assignment', 'off_user.id=auth_assignment.user_id')
                ->select('off_user.id,off_user.name,off_user_department.name dname')
                ->andWhere(['off_user.username'=>$username])
                ->andWhere('off_user.is_staff = 1')
                ->andWhere(['company_categroy_id'=>$people['company_categroy_id']])
                ->andWhere(['<>','off_user.id',$user_id])
                ->andWhere(['<>','off_user.department_id','4'])
                ->andWhere(['<>','auth_assignment.item_name','deliver'])
                ->asArray()
                ->all();
            if (empty($result)){
                $this->setError('未查询到相关信息');
                return false;
            }
            //根据姓名查询审批人
        }elseif (empty($username) && !empty($name)){
            $result = User::find()
                ->leftJoin('off_user_department','off_user_department.id=off_user.department_id')
                ->leftJoin('auth_assignment', 'off_user.id=auth_assignment.user_id')
                ->select('off_user.id,off_user.name,off_user_department.name dname')
                ->andWhere(['like','off_user.name',$name])
                ->andWhere('off_user.is_staff = 1')
                ->andWhere(['company_categroy_id'=>$people['company_categroy_id']])
                ->andWhere(['<>','off_user.department_id','4'])
                ->andWhere(['<>','off_user.id',$user_id])
                ->andWhere(['<>','auth_assignment.item_name','deliver'])
                ->asArray()
                ->all();
            if (empty($result)){
                $this->setError('未查询到相关信息');
                return false;
            }
            //根据两个一块查询
        }elseif (!empty($username) && !empty($name)){
            $result = User::find()
                ->leftJoin('off_user_department','off_user_department.id=off_user.department_id')
                ->leftJoin('auth_assignment', 'off_user.id=auth_assignment.user_id')
                ->select('off_user.id,off_user.name,off_user_department.name dname')
                ->andWhere(['off_user.name'=>$name])
                ->andWhere(['off_user.username'=>$username])
                ->andWhere('off_user.is_staff = 1')
                ->andWhere(['company_categroy_id'=>$people['company_categroy_id']])
                ->andWhere(['<>','off_user.department_id','4'])   //去除离职部的人员
                ->andWhere(['<>','off_user.id',$user_id])
                ->andWhere(['<>','auth_assignment.item_name','deliver'])
                ->asArray()
                ->all();
            if (empty($result)){
                $this->setError('未查询到相关信息');
                return false;
            }
        }elseif (empty($username) && empty($name)){
            $this->setError('请输入查询条件');
            return false;
        }
        //查询公司名
        $company = CompanyCategroy::find()->select('name')->where(['id'=>$people->company_categroy_id])->one();
        foreach ($result as $key => $value)
        {
            if (empty($result[$key]['dname'])){
                $result[$key]['dname'] = '';
            }else{
                $result[$key]['dname'] = $company->name . $result[$key]['dname'];
            }
        }
        return $result;
    }
    /*
     * --------------------------------------------------------------------------------------------------------
     * 获取审批人
     * */
    /**
     * @param $user_id
     * @return array|bool|int
     */
    public function GetApprover($user_id)
    {
        if(!$user_id) {
            $this->setError('用户ID不能为空');
            return false;
        }
        $people = User::find()
            ->andWhere('id = :user',[':user'=>$user_id])
            ->one();
        if(!$people) {
            $this->setError('员工不存在!');
            return false;
        }
        //判断是注册公司还是子公司
        $type = CompanyCategroy::find()
            ->select('fly')
            ->where(['id'=>$people->company_categroy_id])
            ->asArray()
            ->all();
            //云媒主公司 显示公司所有员工及部门3
        if ($people->company_categroy_id == 1 && $type[0]['fly'] == 0 && $people->rank != 3){
            return $this->mainApprover($people,$user_id);
            //注册公司 显示公司所有员工及部门3
        }elseif ($people->company_categroy_id != 1 && $type[0]['fly'] == 0  && $people->rank != 3){
            return $this->mainApprover($people,$user_id);
            //云媒 子公司员工 显示公司所有员工及部门1
        }elseif ($people->rank == 1 && $people->company_categroy_id != 1 && $type[0]['fly'] == 1 && $people->company_categroy_id != 44){
            return $this->mainApprover($people,$user_id);
            //云媒 子公司经理 显示1
        }elseif ($people->rank == 3 && $people->company_categroy_id != 1 && $type[0]['fly'] == 1 && $people->company_categroy_id != 44){
            return $this->subsidiary();
            //云媒 子公司部门经理 显示公司所有员工及部门1
        }elseif ($people->rank == 4 && $people->company_categroy_id != 1 && $type[0]['fly'] == 1 && $people->company_categroy_id != 44){
            return $this->mainApprover($people,$user_id);
            //云南分公司的一线员工
        }elseif ($people->rank == 1 && $type[0]['fly'] == 1 && $people->company_categroy_id == 44) {
            return $this->branch($people,$user_id);
            //云南分公司的部门经理
        }elseif ($people->rank == 4 && $type[0]['fly'] == 1 && $people->company_categroy_id == 44) {
            return $this->branch($people,$user_id);
            //云南公司的子公司经理
        }elseif ($people->rank == 3 && $type[0]['fly'] == 1 && $people->company_categroy_id == 44) {
            return $this->branch($people,$user_id);
            //所属主公司  但有子公司经理的权限2
        }elseif ($people->rank == 3 && $type[0]['fly'] == 0){
            $this->setError('账号权限有问题！');
            return false;
            //所属子公司  但是有主公司经理的权限1
        }elseif ($people->rank == 30 && $people->company_categroy_id != 1 && $type[0]['fly'] == 1){
            $this->setError('账号权限有问题！');
            return false;
        }
    }

    /**
     * 查询云南分公司的审批人 包含云媒的人员
     * @param $people
     * @param $user_id
     * @return array
     */
    public function branch($people,$user_id)
    {
        //查询 云南分公司的人员
        $result1 = $this->mainApprover($people,$user_id);
        // 查询云媒的所有人员
        $result2 = $this->mainPeople($user_id);
        $result = array_merge($result1, $result2);
        return $result;
    }

    /**
     * 查询 云媒的所有人员
     * @param $user_id
     * @return array|bool
     */
    public function mainPeople($user_id)
    {
        //查询公司名
        $company = CompanyCategroy::find()->select('name')->where(['id'=>1])->one();
        //查询公司的所有部门
        $department = UserDepartment::find()
            ->select('id,name')
            ->andWhere(['company'=>1])
            ->andWhere(['is_show'=>1])      //显示  的部门
            ->andWhere(['parent_id'=>0])     //主部门 一级部门
            ->andWhere(['<>','id','4'])      //去除离职部
            ->orderBy('priority desc')
            ->asArray()
            ->all();
        if (!$department) {
            $this->setError('公司暂无部门！');
            return false;
        }
        //遍历部门显示 部门下所有的人员 在职 除去自己
        foreach ($department as $key=>$value)
        {
            $result2['name'] = $company->name . $value['name'];
            $result2['type'] = 0;
            $result2['people'] = User::find()
                ->leftJoin('off_user_department','off_user_department.id=off_user.department_id')
                ->leftJoin('auth_assignment', 'off_user.id=auth_assignment.user_id')
                ->select('off_user.id,off_user.name,off_user_department.name as dname')
                ->andwhere('off_user.company_categroy_id = :company_categroy_id',[':company_categroy_id'=>1])
                ->andWhere('off_user.is_staff = 1')
                ->andWhere(['off_user.department_id'=>$value['id']])
                ->andWhere(['<>','off_user.id',$user_id])
                ->andWhere(['<>','auth_assignment.item_name','deliver'])
                ->orderBy('id asc')
                ->asArray()
                ->all();
            $result[] = $result2;
        }
        //查询没有部门的人 type 区分 是有无部门 1 没有部门 0 有部门
        $other =  User::find()
            ->leftJoin('auth_assignment', 'off_user.id=auth_assignment.user_id')
            ->select('off_user.id,off_user.name')
            ->andWhere(['off_user.is_staff'=>1])
            ->andWhere(['off_user.company_categroy_id'=>1])
            ->andWhere(['<>','off_user.id',$user_id])
            ->andWhere(['off_user.department_id'=>''])
            ->andWhere(['<>','auth_assignment.item_name','deliver'])
            ->asArray()
            ->all();
        if ($other){
            foreach ($other as $value)
            {
                $test['id'] = $value['id'];
                $test['name'] = $value['name'];
                $test['type'] = 1;
                $result[]= $test;
            }
        }
        return $result;
    }
    /**
     * 子公司经理 显示主公司总经理账号 还有主公司的实施部和城市运营部 所有的人
     * @return array
     */
    public function subsidiary()
    {
        //查询实施部
        $shishi =  User::find()
            ->leftJoin('off_user_department','off_user_department.id=off_user.department_id')
            ->leftJoin('auth_assignment', 'off_user.id=auth_assignment.user_id')
            ->select('off_user.id,off_user.name,off_user_department.name as dname')
            ->andwhere('off_user.company_categroy_id = :company_categroy_id',[':company_categroy_id'=>1])
            ->andwhere('off_user.department_id = :department_id',[':department_id'=>16])
            ->andWhere('off_user.is_staff = 1')
            ->andWhere(['<>','auth_assignment.item_name','deliver'])
            ->asArray()
            ->all();
        //查询城市运营部
        $chengshi = User::find()
            ->leftJoin('off_user_department','off_user_department.id=off_user.department_id')
            ->leftJoin('auth_assignment', 'off_user.id=auth_assignment.user_id')
            ->select('off_user.id,off_user.name,off_user_department.name as dname')
            ->andwhere('off_user.company_categroy_id = :company_categroy_id',[':company_categroy_id'=>1])
            ->andwhere('off_user.department_id = :department_id',[':department_id'=>22])
            ->andWhere('off_user.is_staff = 1')
            ->andWhere(['<>','auth_assignment.item_name','deliver'])
            ->asArray()
            ->all();
        //查询主公司的总经理 总经办的人
        $manage = User::find()
            ->leftJoin('off_user_department','off_user_department.id=off_user.department_id')
            ->leftJoin('auth_assignment', 'off_user.id=auth_assignment.user_id')
            ->select('off_user.id,off_user.name,off_user_department.name as dname')
            ->andwhere('off_user.rank = :rank',[':rank'=>30])
            ->andwhere('off_user.company_categroy_id = :company_categroy_id',[':company_categroy_id'=>1])
            ->andwhere('off_user.department_id = :department_id',[':department_id'=>27])
            ->andWhere('off_user.is_staff = 1')
            ->andWhere(['<>','auth_assignment.item_name','deliver'])
            ->asArray()
            ->all();
        $test1['name'] = '总经办';
        $test1['type'] = 0;
        $test1['people'] = $manage;
        $test2['name'] = '实施部';
        $test2['type'] = 0;
        $test2['people'] = $shishi;
        $test3['name'] = '城市运营部';
        $test3['type'] = 0;
        $test3['people'] = $chengshi;
        $result[] = $test1;
        $result[] = $test2;
        $result[] = $test3;
        return $result;
    }
    /**
     * @param $people
     * @param $user_id
     * 公司显示所有部门以及所有的人
     * @return array
     */
    public function mainApprover($people,$user_id)
    {
        //查询公司名
        $company = CompanyCategroy::find()->select('name')->where(['id'=>$people->company_categroy_id])->one();
        //查询公司的所有部门
        $department = UserDepartment::find()
            ->select('id,name')
            ->andWhere(['company'=>$people->company_categroy_id])
            ->andWhere(['is_show'=>1])      //显示  的部门
            ->andWhere(['parent_id'=>0])     //主部门 一级部门
            ->andWhere(['<>','id','4'])      //去除离职部
            ->orderBy('priority desc')
            ->asArray()
            ->all();
        if (!$department) {
            $this->setError('公司暂无部门！');
            return false;
        }
        //遍历部门显示 部门下所有的人员 在职 除去自己
        foreach ($department as $key=>$value)
        {
            $result2['name'] = $company->name . $value['name'];
            $result2['type'] = 0;
            $result2['people'] = User::find()
                ->leftJoin('off_user_department','off_user_department.id=off_user.department_id')
                ->leftJoin('auth_assignment', 'off_user.id=auth_assignment.user_id')
                ->select('off_user.id,off_user.name,off_user_department.name as dname')
                ->andwhere('off_user.company_categroy_id = :company_categroy_id',[':company_categroy_id'=>$people->company_categroy_id])
                ->andWhere('off_user.is_staff = 1')
                ->andWhere(['off_user.department_id'=>$value['id']])
                ->andWhere(['<>','off_user.id',$user_id])
                ->andWhere(['<>','auth_assignment.item_name','deliver'])
                ->orderBy('id asc')
                ->asArray()
                ->all();
            $result[] = $result2;
        }
        //查询没有部门的人 type 区分 是有无部门 1 没有部门 0 有部门
        $other =  User::find()
            ->leftJoin('auth_assignment', 'off_user.id=auth_assignment.user_id')
            ->select('off_user.id,off_user.name')
            ->andWhere(['off_user.is_staff'=>1])
            ->andWhere(['off_user.company_categroy_id'=>$people->company_categroy_id])
            ->andWhere(['<>','off_user.id',$user_id])
            ->andWhere(['off_user.department_id'=>''])
            ->andWhere(['<>','auth_assignment.item_name','deliver'])
            ->asArray()
            ->all();
        if ($other){
            foreach ($other as $value)
            {
                $test['id'] = $value['id'];
                $test['name'] = $value['name'];
                $test['type'] = 1;
                $result[]= $test;
            }
        }
        return $result;
    }
}