<?php
namespace app\services;

use app\foundation\Service;
class GetRankService extends Service
{
	public function getRank()
	{
		$result = [
			'visit' => '拜访客户',
			'register' => '注册数量',
			'register' => '自己注册',
			'register_spread' => '传播注册',
			'order_num' => '订单数量',
			'order_amount' => '订单金额',
			'pre_deposit' => '预存款',
			'pre_money' => '预存款金额',
		];
		return $result;
	}
}