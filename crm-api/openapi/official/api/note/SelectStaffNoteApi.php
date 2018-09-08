<?php
namespace official\api\note;

use Yii;
use app\foundation\Api;
use app\services\NoteService;
use official\Identity;

class SelectStaffNoteApi extends Api
{
    public function run()
    {
        $user_id=Yii::$app->user->id;
        $service=NoteService::instance();
        $result=$service->select($user_id);
        if($result===false)
        {
           return $this->logicError($service->error, $service->errors);
        }
        return ['result'=>$result];
    }
}