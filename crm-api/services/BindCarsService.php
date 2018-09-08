<?php
namespace app\services;

use app\foundation\Service;

use Yii;
use app\models\BindCar;
class BindCarsService extends Service
{
	/**
	 * 绑定车辆（改版后）
	 * @param  [type] $user_id        [用户id]
	 * @param  [type] $car_id         [车辆id]
	 * @param  [type] $is_cooperation [是否为云媒]
	 * @return [type]                 [description]
	 */
	public function bindCars($user_id, $car_id, $is_cooperation, $car_name)
	{
		if(!$user_id || !$car_id || !$is_cooperation || !$car_name){
			$this->setError('参数不能为空');
			return false;
		}
		if($is_cooperation == 0){

			$bind = BindCar::findOne(['user_id' => $user_id]);
			if($bind){
				return [
					'ret' => 28,
					'msg' => [
						'car_id' => $bind->car_id,
						'car_name' => $bind->car_name
					],
				];
			} else {
				$user = User::findOne($user_id);
				$result = new BindCar();
				$result->user_id = $user_id;
				$result->car_name = $car_name;
				$result->car_id = $car_id;
				$result->user_name = $name;
				$result->user_phone = $phone;
				if(!$result->save()){
					$this->setError('车辆绑定失败');
					return false;
				}
				return $result = '绑定成功';
			}
		}
		
	}
	/**
	 * 解绑车辆(z暂时废弃)
	 * @param  [type] $user_id [用户id]
	 * @return [type]          [description]
	 */
	public function bundCars($user_id)
	{
		if(!$user_id){
			$this->setError('参数不能为空');
			return false;
		}
		$result = BindCar::findOne(['user_id' => $user_id]);
		if($result->delete()){
			return $result = '解绑成功';
		}
		return $result = '解绑失败';
	}
}