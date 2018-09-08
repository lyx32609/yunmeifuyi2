<?php
namespace app\services;

use app\foundation\Service;
use app\models\UserDepartment;
use app\models\UserGroup;
use app\models\UserLocation;
use app\benben\DateHelper;
use app\models\User;

class GetDepartmentLocationService extends Service
{
	/**
	 * 定位数据导出
	 * @param  [type] $department_id [部门id]
	 * @param  [type] $user_id       [用户id]
	 * @param  [type] $type          [1本日 2本周 3本月]
	 * @return [type]                [description]
	 */
	public function getDepartmentLocation($department_id, $user_id, $type)
	{
		if($type == '1'){
			$start = DateHelper::getTodayStartTime();
        	$end = DateHelper::getTodayEndTime();
		}
		if($type == '2'){
			$start = DateHelper::getWeekStartTime(0);
			$end = DateHelper::getThisWeekEndTime(0);
		}
		if($type == '3'){
			$start = DateHelper::getMonthStartTime(0);
			$end = DateHelper::getMonthEndTime(0);
		}
		if($department_id) {
			$department = UserDepartment::find()
					->select(['domain_id'])
					->where(['id' => $department_id])
					->andWhere(['is_show' => 1])
					->asArray()
					->one();
			if(!$department){
				$this->setError('部门不存在');
				return false;
			}
			$group = UserGroup::find()
					->select(['id'])
					->where(['department_id' => $department_id])
					->asArray()
					->all();
			if(!$group){
				$user = User::find()
						->select(['username'])
						->where(['department_id' => $department_id])
						->andWhere(['domain_id' => $department['id']])
						->asArray()
						->all();
				
			} else {
				for($i = 0; $i < count($group); $i++){
					$group_id[$i] = $group[$i]['id'];
				}
				$user = User::find()
					->select(['username'])
					->where(['department_id' => $department_id])
					->andWhere(['domain_id' => $department['domain_id']])
					->andWhere(['in', 'group_id', $group_id])
					->asArray()
					->all();
			}
			if(!$user){
				$this->setError(['无用户数据']);
				return false;
			}
			for($i = 0; $i < count($user); $i++){
					$list[$i] = $user[$i]['username'];
			}
			$list = $this->getLocation($list, $start, $end);
			if(!$list){
				$this->setError('暂无数据');
				return false;
			}
		}
		if($user_id) {
			$user = User::find()
					->select(['username'])
					->where(['id' => $user_id])
					->asArray()
					->one();
			if(!$user){
				$this->setError('用户不存在');
				return false;
			}
			$list = $this->getLocation($user['username'], $start, $end);
			if(!$list){
				$this->setError('暂无数据');
				return false;
			}
		}
		$result = $this->checkOk($list);

		return $result;
	}
	/**
	 * 处理定位是否合理
	 * @param  [type] $result [description]
	 * @return [type]         [description]
	 */
	public function checkOK($result)
	{
		$list = array();
      	for ($i = 0; $i < count($result); $i++) {
        $list[$i]['locationdate'] = date('Y-m-d H:i:s',$result[$i]['time']);
        if($result[$i]['belong'] == 1){
        	$list[$i]['shopName'] = $result[$i]['shops']['shopname']?$result[$i]['shops']['shopname']:'';
        } else {
        	$list[$i]['shopName'] = $result[$i]['shops']['company_name']?$result[$i]['shops']['company_name']:'';
        } 
        $list[$i]['shopLongitude'] = $result[$i]['shops']['longitude']?$result[$i]['shops']['longitude']:'';
        $list[$i]['shopLatitude'] = $result[$i]['shops']['latitude']?$result[$i]['shops']['latitude']:'';
        $list[$i]['longitude'] = $result[$i]['longitude']?$result[$i]['longitude']:'';
        $list[$i]['latitude'] = $result[$i]['latitude']?$result[$i]['latitude']:'';
        $list[$i]['shop_ip'] = $result[$i]['shop_id']?$result[$i]['shop_id']:'';
        $list[$i]['user'] = $result[$i]['user']?$result[$i]['user']:'';
        $list[$i]['belong'] = $result[$i]['belong']?$result[$i]['belong']:'';
        $list[$i]['name'] = $result[$i]['name']?$result[$i]['name']:'';
        if ($result[$i]['shop_id']!=0){
            $shop_longitude = sprintf("%.3f",substr(sprintf("%.4f", $result[$i]['shops']['longitude']), 0,-1));
            $shop_latitude =  sprintf("%.3f",substr(sprintf("%.4f", $result[$i]['shops']['latitude']), 0,-1));
            $res_longitude = sprintf("%.3f",substr(sprintf("%.4f", $result[$i]['longitude']), 0,-1));
            $res_latitude = sprintf("%.3f",substr(sprintf("%.4f", $result[$i]['latitude']), 0,-1));
            if ((abs($shop_longitude-$res_longitude))>0||(abs($shop_latitude-$res_latitude))>0){
                $list[$i]['isOk'] = '不合理';
            }else {
                $list[$i]['isOk'] = '合理';
            }
        }else{
            $list[$i]['isOk'] = '';
        }
      }
      return  $list;
	}
	/**
	 * 查询员工业务定位记录
	 * @param  [type] $user  [用户名]
	 * @param  [type] $start [开始时间]
	 * @param  [type] $end   [结束时间]
	 * @return [type]        [description]
	 */
	public function getLocation($user, $start, $end)
	{
		if(is_array($user)){
			$list = UserLocation::find()
					->select(['shop_id', 'name', 'user', 'time', 'longitude', 'latitude', 'belong'])
					->where(['in', 'user', $user])
					->andWhere(['between', 'time', $start, $end])
					->andWhere('belong != :belong ', [':belong' => '0'])
					->andWhere(['type' => '0'])
					->andWhere(['type' ])
					->orderBy('user desc')
					->asArray()
					->all();
		}
		if(is_string($user)){
			$list = UserLocation::find()
					->select(['shop_id', 'name', 'user', 'time', 'longitude', 'latitude', 'belong'])
					->where(['user' => $user])
					->andWhere(['between', 'time', $start, $end])
					->andWhere('belong != :belong ', [':belong' => '0'])
					->andWhere(['type' => '0'])
					->orderBy('time desc')
					->asArray()
					->all();
		}
		for($i = 0; $i < count($list); $i++){
        	if($list[$i]['belong'] == 1){ 
                $member=\Yii::$app->api->request('basic/getMember',['member_id'=>$list[$i]['shop_id']]);
                if($member['ret']===0)
                {
                    $list[$i]['shops']=$member[0];
                }else {
                    $list[$i]['shops']['name']='采购商编号：'.$list[$i]['shop_id'].'无信息';
                    $list[$i]['shops']['longitude']=0;
                    $list[$i]['shops']['latitude']=0;
                }
            }else  if($list[$i]['belong'] == 2){
                $supplier=\Yii::$app->api->request('basic/getSupplier',['supplierId' => $list[$i]['shop_id']]);
            	if($supplier['ret']===0)
                {
                  $list[$i]['shops']=$supplier[0];
          		}else{
                    $list[$i]['shops']['company_name']='供货商编号：'.$list[$i]['shop_id'].'无信息';
                    $list[$i]['shops']['longitude']=0;
                    $list[$i]['shops']['latitude']=0;
                }
                /*  $row['shops']=Supplier::find()
                    ->select(['uid','company_name as shopname','longitude','latitude'])
                    ->andWhere('uid=:uid',[':uid'=>$row['shop_id']])
                    ->asArray()->one(); */
            }else if($rows[$i]['belong'] == 0){
              if($rows[$i]['belong'] == 1){ 
                  $member=\Yii::$app->api->request('basic/getMember',['member_id'=>$rows[$i]['shop_id']]);
                  if($member['ret']===0)
                  {
                      $rows[$i]['shops']=$member[0];
                  }else {
                      $rows[$i]['shops']['name']='采购商编号：'.$rows[$i]['shop_id'].'无信息';
                      $rows[$i]['shops']['longitude']=0;
                      $rows[$i]['shops']['latitude']=0;
                  }
              }else  if($rows[$i]['belong'] == 2){
                  $supplier=\Yii::$app->api->request('basic/getSupplier',['supplierId' => $rows[$i]['shop_id']]);
                if($supplier['ret']===0)
                  {
                    $rows[$i]['shops']=$supplier[0];
                  }else{
                      $rows[$i]['shops']['company_name']='供货商编号：'.$rows[$i]['shop_id'].'无信息';
                      $rows[$i]['shops']['longitude']=0;
                      $rows[$i]['shops']['latitude']=0;
                  }
                  /*  $row['shops']=Supplier::find()
                      ->select(['uid','company_name as shopname','longitude','latitude'])
                      ->andWhere('uid=:uid',[':uid'=>$row['shop_id']])
                      ->asArray()->one(); */
              }
            }
           
        }
         return $list;
    }

	/**
	 * 查询员工信息
	 * @param  [type] $user [description]
	 * @return [type]       [description]
	 */
	public function selectUser($user)
	{
		$data = User::find()
				->select(['name'])
				->where(['username' => $user])
				->asArray()
				->one();
		return $data;
	}
}