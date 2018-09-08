<?php
namespace app\services;

use app\foundation\Service;
use app\models\Regions;
class GetAreaCityService extends Service
{
	/**
	 * 联动查询城市
	 * @param  [type] $area [description]
	 * @return [type]       [description]
	 */
	public function getAreaCity()
	{
		$result = Regions::find()
				->select(Regions::tableName().'.region_id,local_name')
					->with(['regions' => function (\yii\db\ActiveQuery $query) { 
	              			$query->select('p_region_id,region_id,local_name,p_region_id')->where('region_grade = :region_grade',[':region_grade' => 2]);
	          			}])
					->where(Regions::tableName() .'.region_grade = :region_grade',[':region_grade' => 1])
					->asArray()
					->all();
		return $result;
		
	}
}