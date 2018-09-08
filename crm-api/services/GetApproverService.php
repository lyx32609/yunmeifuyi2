<?php
namespace app\services;

use app\foundation\Service;
use app\models\CompanyCategroy;
use app\models\User;
use app\models\UserDepartment;

class GetApproverService extends Service
{
    /**
     * @param $user_id
     * @return array|bool|string|\yii\db\ActiveRecord[]
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
        //主公司一线员工  跟注册企业一线员工一样获取 该公司的 所有的部门经理和总经理       19
        if ($people->rank == 1 && $people->company_categroy_id == 1 && $type[0]['fly'] == 0){
            return $this->staffApprover($people);
        //注册企业的一线员工  同上                                                     992
        }elseif ($people->rank == 1 && $people->company_categroy_id != 1 && $type[0]['fly'] == 0) {
            return $this->regstaffApprover($people);
        //子公司一线员工  获取  该子公司的部门经理和 该子公司经理                        240
        }elseif ($people->rank == 1 && $people->company_categroy_id != 1 && $type[0]['fly'] == 1){
            return $this->sonStaffApprover($people);
        //子公司经理  主公司的实施部、城市运营部经理和主公司的总经理                        28
        }elseif ($people->rank == 3){
            return $this->sonCompanyApprover();
        //主公司部门经理   该公司的部门经理（除自己）+总经理                               32
        }elseif ($people->rank == 4 && $people->company_categroy_id == 1 && $type[0]['fly'] == 0){
            return $this->manageApprover($people,$user_id);
        //注册企业的部门经理 同上                                                          980
        }elseif ($people->rank == 4 && $people->company_categroy_id != 1 && $type[0]['fly'] == 0) {
            return $this->regmanageApprover($people,$user_id);
         //子公司的部门经理 该子公司的部门经理（除自己）和 该子公司经理                      416
        }elseif ($people->rank == 4 && $people->company_categroy_id != 1 && $type[0]['fly'] == 1){
            return $this->sonManageApprover($people,$user_id);
        //主公司经理
        }elseif ($people->rank ==30 && $people->company_categroy_id == 1){
            return $this->mainManageApprover($people,$user_id);
        //注册主公司经理
        }elseif ($people->rank ==30 && $people->company_categroy_id != 1){
            return $this->regmainManageApprover($people,$user_id);
        }
    }
    //注册主公司经理 显示 自己公司对应的其他的主公司 经理
    public function regmainManageApprover($people, $user_id)
    {

        //查询主公司的总经理
        $manage = User::find()
            ->select('off_user.id,off_user.name,off_user_department.name dname')
            ->leftJoin('off_user_department','off_user_department.id=off_user.department_id')
            ->andwhere('off_user.rank = :rank',[':rank'=>30])
            ->andwhere('off_user.company_categroy_id = :company_categroy_id',[':company_categroy_id'=>$people->company_categroy_id])
            ->andWhere('off_user.is_staff = 1')
            ->orderBy('off_user_department.priority desc,off_user.id asc')
            ->asArray()
            ->all();
        //除去自己
        foreach ($manage as $key=>$value)
        {
            if ($manage[$key]['id'] == $user_id){
                unset($manage[$key]);
            }
        }
        foreach ($manage as $key => $value)
        {
            if (empty($manage[$key]['dname'])){
                $manage[$key]['dname'] = '总经办';
            }
        }

        $result = array_values($manage);
        return $result;
    }
     //主公司经理 显示 自己公司对应的其他的主公司 经理
    public function mainManageApprover($people, $user_id)
    {
        //查询主公司的总经理
        $manage = User::find()
            ->select('off_user.id,off_user.name,off_user_department.name dname')
            ->leftJoin('off_user_department','off_user_department.id=off_user.department_id')
            ->andwhere('off_user.rank = :rank',[':rank'=>30])
            ->andwhere('off_user.company_categroy_id = :company_categroy_id',[':company_categroy_id'=>$people->company_categroy_id])
            ->andWhere('off_user.is_staff = 1')
            ->orderBy('off_user_department.priority desc,off_user.id asc')
            ->asArray()
            ->all();
        //除去自己
        foreach ($manage as $key=>$value)
        {
            if ($manage[$key]['id'] == $user_id){
                unset($manage[$key]);
            }
        }
        foreach ($manage as $key => $value)
        {
            if (empty($manage[$key]['dname'])){
                $manage[$key]['dname'] = '总经办';
            }
        }

        $result = array_values($manage);
        return $result;
    }

    //子公司的部门经理 该子公司的部门经理（除自己）和 该子公司经理
    public function sonManageApprover($people,$user_id)
    {
        //子公司的部门经理（除自己）
        $result1 =  User::find()
            ->select('off_user.id,off_user.name,off_user_department.name dname')
            ->leftJoin('off_user_department','off_user_department.id=off_user.department_id')
            ->andwhere('off_user.company_categroy_id = :company_categroy_id',[':company_categroy_id'=>$people->company_categroy_id])
            ->andwhere('off_user.rank = :rank',[':rank'=>4])
            ->andWhere('off_user.is_staff = 1')
            ->orderBy('off_user_department.priority desc,off_user.id asc')
            ->asArray()
            ->all();
        //子公司的经理
        $result2 =  User::find()
            ->select('off_user.id,off_user.name,off_user_department.name dname')
            ->leftJoin('off_user_department','off_user_department.id=off_user.department_id')
            ->andwhere('off_user.company_categroy_id = :company_categroy_id',[':company_categroy_id'=>$people->company_categroy_id])
            ->andwhere('off_user.rank = :rank',[':rank'=>3])
            ->andWhere('off_user.is_staff = 1')
            ->orderBy('off_user_department.priority desc,off_user.id asc')
            ->asArray()
            ->all();
        $result = array_merge($result2,$result1);
        if (!$result){
            $result = '审批人未找到！';
        }
        //除去自己
        foreach ($result as $key=>$value)
        {
            if ($result[$key]['id'] == $user_id){
                unset($result[$key]);
            }
        }
        foreach ($result as $key => $value)
        {
            if (empty($result[$key]['dname'])){
                $result[$key]['dname'] = '';
            }
        }
        $result = array_values($result);
        return $result;
    }
    //主公司的部门经理
    public function manageApprover($people,$user_id)
    {
        //主公司的部门经理
        $result1 =  User::find()
            ->select('off_user.id,off_user.name,off_user_department.name dname')
            ->leftJoin('off_user_department','off_user_department.id=off_user.department_id')
            ->andwhere('off_user.company_categroy_id = :company_categroy_id',[':company_categroy_id'=>$people->company_categroy_id])
            ->andwhere('off_user.rank = :rank',[':rank'=>4])
            ->andWhere('off_user.is_staff = 1')
            ->orderBy('off_user_department.priority desc,off_user.id asc')
            ->asArray()
            ->all();
        //公司的总经理
        $result2 =  User::find()
            ->select('off_user.id,off_user.name,off_user_department.name dname')
            ->leftJoin('off_user_department','off_user_department.id=off_user.department_id')
            ->andwhere('off_user.company_categroy_id = :company_categroy_id',[':company_categroy_id'=>$people->company_categroy_id])
            ->andwhere('off_user.rank = :rank',[':rank'=>30])
            ->andWhere('off_user.is_staff = 1')
            ->orderBy('off_user_department.priority desc,off_user.id asc')
            ->asArray()
            ->all();

        foreach ($result2 as $key => $value)
        {
            if (empty($result2[$key]['dname'])){
                $result2[$key]['dname'] = '总经办';
            }
        }
        $result = array_merge($result2,$result1);
        if (!$result){
            $result = '审批人未找到！';
        }
        //除去自己
        foreach ($result as $key=>$value)
        {
            if ($result[$key]['id'] == $user_id){
                unset($result[$key]);
            }
        }

        $result = array_values($result);
        return $result;
    }
    //子公司经理   主公司的实施部、城市运营部经理和主公司的总经理
    public function sonCompanyApprover()
    {
        //查询实施部
        $shishi =  User::find()
            ->select('off_user.id,off_user.name,off_user_department.name dname')
            ->leftJoin('off_user_department','off_user_department.id=off_user.department_id')
            ->andwhere('off_user.rank = :rank',[':rank'=>4])
            ->andwhere('off_user.company_categroy_id = :company_categroy_id',[':company_categroy_id'=>1])
            ->andwhere('off_user.department_id = :department_id',[':department_id'=>16])
            ->andWhere('off_user.is_staff = 1')
            ->asArray()
            ->all();
        //查询城市运营部
        $chengshi = User::find()
            ->select('off_user.id,off_user.name,off_user_department.name dname')
            ->leftJoin('off_user_department','off_user_department.id=off_user.department_id')
            ->andwhere('off_user.rank = :rank',[':rank'=>4])
            ->andwhere('off_user.company_categroy_id = :company_categroy_id',[':company_categroy_id'=>1])
            ->andwhere('off_user.department_id = :department_id',[':department_id'=>22])
            ->andWhere('off_user.is_staff = 1')
            ->asArray()
            ->all();
        //查询主公司的总经理
        $manage = User::find()
            ->select('off_user.id,off_user.name,off_user_department.name dname')
            ->leftJoin('off_user_department','off_user_department.id=off_user.department_id')
            ->andwhere('off_user.rank = :rank',[':rank'=>30])
            ->andwhere('off_user.company_categroy_id = :company_categroy_id',[':company_categroy_id'=>1])
            ->andWhere('off_user.is_staff = 1')
            ->orderBy('off_user_department.priority desc,off_user.id asc')
            ->asArray()
            ->all();
        foreach ($manage as $key => $value)
        {
            if (empty($manage[$key]['dname'])){
                $manage[$key]['dname'] = '总经办';
            }
        }
        $result = array_merge($manage,$chengshi,$shishi);
        if (!$result){
            $result = '审批人未找到！';
        }
        return $result;
    }
    //主公司的一线员工或者配送人员
    public function staffApprover($people)
    {
        //查询该公司的部门经理
        $result1 =  User::find()
            ->select('off_user.id,off_user.name,off_user_department.name dname')
            ->leftJoin('off_user_department','off_user_department.id=off_user.department_id')
            ->andwhere('off_user.company_categroy_id = :company_categroy_id',[':company_categroy_id'=>$people->company_categroy_id])
            ->andwhere('off_user.rank = :rank',[':rank'=>4])
            ->andWhere('off_user.is_staff = 1')
            ->orderBy('off_user_department.priority desc,off_user.id asc')
            ->asArray()
            ->all();
        //查询该公司的主公司经理
        $result2 =  User::find()
            ->select('off_user.id,off_user.name,off_user_department.name dname')
            ->leftJoin('off_user_department','off_user_department.id=off_user.department_id')
            ->andwhere('off_user.company_categroy_id = :company_categroy_id',[':company_categroy_id'=>$people->company_categroy_id])
            ->andwhere('off_user.rank = :rank',[':rank'=>30])
            ->andWhere('off_user.is_staff = 1')
            ->orderBy('off_user_department.priority desc,off_user.id asc')
            ->asArray()
            ->all();
        foreach ($result2 as $key => $value)
        {
            if (empty($result2[$key]['dname'])){
                $result2[$key]['dname'] = '总经办';
            }
        }
        $result = array_merge($result2,$result1);
        if (!$result){
            $result = '审批人未找到！';
        }
        return $result;
    }
    //子公司一线员工
    public function sonStaffApprover($people)
    {
        //查询子公司的部门经理
        $result1 =  User::find()
            ->select('off_user.id,off_user.name,off_user_department.name dname')
            ->leftJoin('off_user_department','off_user_department.id=off_user.department_id')
            ->andwhere('off_user.company_categroy_id = :company_categroy_id',[':company_categroy_id'=>$people->company_categroy_id])
            ->andwhere('off_user.rank = :rank',[':rank'=>4])
            ->andWhere('off_user.is_staff = 1')
            ->orderBy('off_user_department.priority desc,off_user.id asc')
            ->asArray()
            ->all();
        //查询子公司经理
        $result2 =  User::find()
            ->select('off_user.id,off_user.name,off_user_department.name dname')
            ->leftJoin('off_user_department','off_user_department.id=off_user.department_id')
            ->andwhere('off_user.company_categroy_id = :company_categroy_id',[':company_categroy_id'=>$people->company_categroy_id])
            ->andwhere('off_user.rank = :rank',[':rank'=>3])
            ->andWhere('off_user.is_staff = 1')
            ->orderBy('off_user_department.priority desc,off_user.id asc')
            ->asArray()
            ->all();
        $result = array_merge($result2,$result1);
        if (!$result){
            $result = '审批人未找到！';
        }
        foreach ($result as $key => $value)
        {
            if (empty($result[$key]['dname'])){
                $result[$key]['dname'] = '';
            }
        }
        return $result;
    }
    //注册公司的部门经理
    public function regmanageApprover($people,$user_id)
    {
        //主公司的部门经理
        $result1 =  User::find()
            ->select('off_user.id,off_user.name,off_user_department.name dname')
            ->leftJoin('off_user_department','off_user_department.id=off_user.department_id')
            ->andwhere('off_user.company_categroy_id = :company_categroy_id',[':company_categroy_id'=>$people->company_categroy_id])
            ->andwhere('off_user.rank = :rank',[':rank'=>4])
            ->andWhere('off_user.is_staff = 1')
            ->orderBy('off_user_department.priority desc,off_user.id asc')
            ->asArray()
            ->all();
        //公司的总经理
        $result2 =  User::find()
            ->select('off_user.id,off_user.name,off_user_department.name dname')
            ->leftJoin('off_user_department','off_user_department.id=off_user.department_id')
            ->andwhere('off_user.company_categroy_id = :company_categroy_id',[':company_categroy_id'=>$people->company_categroy_id])
            ->andwhere('off_user.rank = :rank',[':rank'=>30])
            ->andWhere('off_user.is_staff = 1')
            ->orderBy('off_user_department.priority desc,off_user.id asc')
            ->asArray()
            ->all();

        foreach ($result2 as $key => $value)
        {
            if (empty($result2[$key]['dname'])){
                $result2[$key]['dname'] = '总经办';
            }
        }

        $result = array_merge($result2,$result1);
        if (!$result){
            $result = '审批人未找到！';
        }
        //除去自己
        foreach ($result as $key=>$value)
        {
            if ($result[$key]['id'] == $user_id){
                unset($result[$key]);
            }
        }

        $result = array_values($result);
        return $result;
    }
    //注册企业的一线员工或者配送人员
    public function regstaffApprover($people)
    {
        //查询该公司的部门经理
        $result1 =  User::find()
            ->select('off_user.id,off_user.name,off_user_department.name dname')
            ->leftJoin('off_user_department','off_user_department.id=off_user.department_id')
            ->andwhere('off_user.company_categroy_id = :company_categroy_id',[':company_categroy_id'=>$people->company_categroy_id])
            ->andwhere('off_user.rank = :rank',[':rank'=>4])
            ->andWhere('off_user.is_staff = 1')
            ->orderBy('off_user_department.priority desc,off_user.id asc')
            ->asArray()
            ->all();
        //查询该公司的主公司经理
        $result2 =  User::find()
            ->select('off_user.id,off_user.name,off_user_department.name dname')
            ->leftJoin('off_user_department','off_user_department.id=off_user.department_id')
            ->andwhere('off_user.company_categroy_id = :company_categroy_id',[':company_categroy_id'=>$people->company_categroy_id])
            ->andwhere('off_user.rank = :rank',[':rank'=>30])
            ->andWhere('off_user.is_staff = 1')
            ->orderBy('off_user_department.priority desc,off_user.id asc')
            ->asArray()
            ->all();
        foreach ($result2 as $key => $value)
        {
            if (empty($result2[$key]['dname'])){
                $result2[$key]['dname'] = '总经办';
            }
        }
        $result = array_merge($result2,$result1);
        if (!$result){
            $result = '审批人未找到！';
        }
        return $result;
    }
}