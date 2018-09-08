<?php

namespace official\api\historyRecord;

use app\foundation\Api;
use app\services\HistoryNewService;

/**
 * 获取店铺历史记录 2.1新增接口 添加返回数据  name img
 * @parm int shop_id店铺id
 * @return array ['id'=>历史记录id, 'conte'=>内容, 'date'=>时间,'name'=>业务员名字,'img'=>图片地址]
 * @author lzk
 */
class HistoryRecordNewOneApi extends Api {
	public function run() {
		$shop_id = \Yii::$app->request->post ( 'shop_id' );
		$belong = \Yii::$app->request->post ( 'belong' );
		$is_cooperation = \Yii::$app->request->post('is_cooperation');
		$service = HistoryNewService::instance ();
		$data = $service->getHistoryRecordsData ( $shop_id, $belong , $is_cooperation);
		if ($data === false) {
			return $this->logicError ( $service->error, $service->errors );
		}
		return [ 
				'historyRecord' => $data 
		];
	}
}