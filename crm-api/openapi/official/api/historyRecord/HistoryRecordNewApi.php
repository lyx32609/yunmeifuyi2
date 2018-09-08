<?php

namespace official\api\historyRecord;

use app\foundation\Api;
use app\services\HistoryService;

/**
 * 获取店铺历史记录
 * @parm int shop_id店铺id
 * 
 * @return array ['id'=>历史记录id, 'conte'=>内容, 'date'=>时间]
 * @author lzk
 */
class HistoryRecordNewApi extends Api {
	public function run() {
		$shop_id = \Yii::$app->request->post ( 'shop_id' );
		$belong = \Yii::$app->request->post ( 'belong' );
		$service = HistoryService::instance ();
		$data = $service->getHistoryRecords ( $shop_id, $belong );
		if ($data === false) {
			return $this->logicError ( $service->error, $service->errors );
		}
		return [ 
				'historyRecord' => $data 
		];
	}
}