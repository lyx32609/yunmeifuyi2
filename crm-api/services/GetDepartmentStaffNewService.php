<?php
namespace app\services;

use app\foundation\Service;
use app\models\UserDepartment;
use app\models\Regions;
use app\models\UserGroup;
use app\models\User;
use app\benben\DateHelper;
use app\models\ShopNote;
use app\models\UserBusiness;
use app\models\UserBusinessNotes;

class GetDepartmentStaffNewService extends Service
{
	/**
	 * 获取部门人员列表
	 * @param  [type] $city       [城市名称]
	 * @param  [type] $department [部门名称]
	 * @return [type]             [需查询公司id]
	 */
	public function getDepartmentStaff($city, $department, $company_category_id)
	{
		if(!$city) {
			$this->setError('城市不能为空');
			return false;
		}
		if(!$department) {
			$this->setError('部门不能为空');
			return false;
		}
		if(!$company_category_id) {
			$this->setError('公司不能为空');
			return false;
		}
		$domain_id = Regions::find()
				->select(['region_id'])
				->where(['local_name' => $city])
				->andWhere(['region_grade' => 2])
				->asArray()
				->one();
		$department_id = UserDepartment::find()
				->select(['id', 'name'])
				->andWhere('domain_id = :domain_id',['domain_id' => $domain_id['region_id']])
				->andWhere('name = :name',['name' => $department])
				->andWhere('company = :company',['company' => $company_category_id])
				->andWhere(['is_show' => 1])
				->asArray()
				->one();
		if(!$department_id){
			$this->setError('暂无部门信息');
			return false;
		}
		$group_department = UserGroup::find()
				->select(['id', 'name'])
				->where(['department_id' => $department_id['id']])
				->asArray()
				->all();
		
		if($group_department) {
			$result = UserGroup::find()
				->select(UserGroup::tableName().'.id,name')
				->with(['users' => function (\yii\db\ActiveQuery $query) {
              			$query->select('group_id, id,name,username');
          			}])
				->where(UserGroup::tableName() .'.department_id = :department',[':department' => $department_id['id']])
				->asArray()
				->all();
			for($i = 0; $i < count($result); $i++) {
				for($j = 0; $j < count($result[$i]['users']); $j++){
					$result[$i]['users'][$j]['staffNote'] = $this->select($result[$i]['users'][$j]['id']);
				}
			}
		} else { 
			$data = User::find()
				->select(['id', 'name', 'username'])
				->where(['department_id' => $department_id['id']])
				->andWhere(['is_staff'=>1])
				->asArray()
				->all();
			if(!$data){
			    $this->setError('暂无人员信息');
			    return false;
			}
			for($i = 0; $i < count($data); $i++) {
				$data[$i]['staffNote'] = $this->select($data[$i]['id']);
			}
			$result = [[
				'id' => 0,
				'name' => '部门人员',
				'users' => $data
			]];
		}
		return $result;
	}
	/*
     * 查询业务人员当日汇报的提交情况
     *  早上 8:30   晚上 5：30 （10月至次年4月）   6:00  （5月至次年9月）
     *  */
    private function select($user_id)
    {
        $s=0;
        $user=User::findOne(['id'=>$user_id]);
        if(!$user)
        {
            $this->setError('用户不存在');
            return false;
        }
        $time=$_SERVER['REQUEST_TIME'];
        $today_start=DateHelper::getTodayStartTime();
        $today_end=DateHelper::getTodayEndTime();
        $real_time=$time-$today_start;   //获取当天的实时时间戳
        $goToWork=$today_start+8.5*3600;
        $m=DateHelper::getMonth();
        if($m>=5&&$m<=9)
        {
            $goOffWord=$today_start+18*3600;
        }else{
            $goOffWord=$today_start+17.5*3600;
        }
        
        $start_note=ShopNote::find()->andWhere(['user'=>$user->username])->andWhere(['between','time',$today_start,$goToWork])->one();            
        $start_user_business=UserBusiness::find()->andWhere(['staff_num'=>$user->username])->andWhere(['between','time',$today_start,$goToWork])->one();            
        $start_user_business_notes=UserBusinessNotes::find()->andWhere(['staff_num'=>$user->username])->andWhere(['between','time',$today_start,$goToWork])->one();           
        if(!$start_note&&!$start_user_business&&!$start_user_business_notes)
        {
            $s=$s+1;
        }
        
        if($time-$goOffWord>0)
        {
            $end_note=ShopNote::find()->andWhere(['user'=>$user->username])->andWhere(['between','time',$goOffWord,$today_end])->one();
            $end_user_business=UserBusiness::find()->andWhere(['staff_num'=>$user->username])->andWhere(['between','time',$goOffWord,$today_end])->one();
            $end_user_business_notes=UserBusinessNotes::find()->andWhere(['staff_num'=>$user->username])->andWhere(['between','time',$goOffWord,$today_end])->one();
            if(!$end_note&&!$end_user_business&&!$end_user_business_notes)
            {
                $s=$s+2;
            }
        }
        return $s;
    }
}