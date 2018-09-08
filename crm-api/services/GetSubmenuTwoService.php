<?php
namespace app\services;

use app\foundation\Service;
use app\models\Problem;
use app\models\UserDepartment;
use app\models\ProviceCity;
use app\models\User;
use app\models\Regions;
use app\services\GetUserIdOrNameService;
class GetSubmenuTwoService extends Service
{
	/**
	 *created by 付腊梅 2017/06/15
	 * 根据id,查询管理员账号显示问题列表
	 * @param  [type] $user_company_id    [登录人所在企业id]
	 * @param  [type] $area       [省份名称]
	 * @param  [type] $city       [城市名称]
	 * @param  [type] $department_name [部门名称]
	 * @param  [type] $department_id [部门id]
	 * @param  [type] $company_name [公司名称]
	 * @param  [type] $company_id [公司id]
	 * @param  [type] $specification [规格： 1本日  2本周 3 本月]
	 * @return [type]             [description]
	 */
	public function getSubmenu($user_company_id,$area,$city,$department_name,$department_id,$company_name,$company_id,$timeType)
	{
		if(!$user_company_id) {
			$this->setError('用户所在企业ID不能为空');
			return false;
		}
		if(!$area)
		{
			$this->setError('省不能为空');
			return false;
		}
		if(!$city)
		{
			$this->setError('市不能为空');
			return false;
		}
		if(!($department_name && $department_id))
		{
			$this->setError('部门不能为空');
			return false;
		}
		if(!($company_name))
		{
			$this->setError('公司名字不能为空');
			return false;
		}
		if(!$timeType){
			$timeType = 1;
		}
		if($timeType == 1) {
			//本日
			$startTime = mktime(0,0,0,date('m'),date('d'),date('Y'));
			$endTime = mktime(0,0,0,date('m'),date('d')+1,date('Y'))-1;
		} else if($timeType == 2) {
			//本周
			$startTime = mktime(0, 0 , 0,date("m"),date("d")-date("w")+1,date("Y"));
			$endTime = time();
		} else if($timeType == 3){
			//本月
			$startTime =mktime(0, 0 , 0,date("m"),1,date("Y"));
			$endTime = time();
		}
		$service = GetUserIdOrNameService::instance();
		$user_data = $service->getuserDataByType($user_company_id, $area, $city, $department_name,
$department_id, $company_name, $company_id,2);
		if(!$user_data)
		{
			$this->setError($area.'省'.$city.'市'.$company_name.'公司'."暂无人员");
			return false;
		}
		$user = explode(",",$user_data);
		$data_all = $this->getProblemByType($user,$startTime,$endTime,1);
		$data_call = $this->getProblemByType($user,$startTime,$endTime,2);

		for($i=0;$i<count($data_all);$i++)
		{
			$data_all[$i]['user'] = User::find()
					->select(['id', 'name', 'department_id', 'domain_id'])
					->where(['id' => $data_all[$i]['user_id']])
					->asArray()
					->one();
			$data_all[$i]['department'] = UserDepartment::find()
					->select(['id', 'name'])
					->where(['id' => $data_all[$i]['user']['department_id']])
					->andWhere(['is_show' => 1])
					->asArray()
					->one();
			$city_name = Regions::find()
					->select(['p_region_id',"local_name"])
					->where(['region_id' => $data_all[$i]['user']['domain_id']])
					->asArray()
					->one();
			$data_all[$i]['city']['city_name'] = $city_name['local_name'];
			$province_name = Regions::find()
					->select(['region_id', 'p_region_id',"local_name"])
					->where(['region_id' => $city_name['p_region_id']])
					->asArray()
					->one();
			$data_all[$i]['city']['province_name'] = $province_name['local_name'];
		}
		for($i = 0; $i < count($data_all); $i++){
			if($data_all[$i]['collaboration_department'] == 'null'){
				$data_all[$i]['collaboration_department'] = '';
			}
		}
		$result['num'] = count($data_all);
		$result['collaboration'] = count($data_call);
		$result['list'] = $data_all;
		return $result;
		//return ['msg'=>$user_data];

		
	}

	/*根据类型获取问题列表*/
	public function getProblemByType($user,$startTime,$endTime,$type)
	{
		if($type == 1)
		{
			$where1 = ["in","user_id",$user];
		}
		if($type == 2)
		{
			$where1 = ['!=', 'collaboration_department', 'null'];//协同问题
		}
		$select = ['problem_id', 'problem_title', 'problem_content', 'user_id', 'priority', 'collaboration_department', 'create_time'];
		$data = Problem::find()
			  ->select($select)
			  ->where(["in","user_id",$user])
			  ->andWhere($where1)
			  ->andWhere(['between','create_time', $startTime, $endTime])
			  ->asArray()
			  ->all();
		return $data;

	}
}