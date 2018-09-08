<?php


namespace official\api\hr;

use app\foundation\Api;
use app\models\Question;
use app\models\User;

class QuestionCompanyApi extends Api
{
    public function run()
    {
        /**
         * 循环更新指令公司id
         * @var [type]
         */
        $result = Question::find()
                ->select(['question_id', 'author_id'])
                ->asArray()
                ->all();
        $list = [];
        for($i = 0; $i < count($result); $i++){
            $list[$i]['company_id'] = $this->getCategory($result[$i]['author_id']);
            $list[$i]['id'] = $result[$i]['question_id'];
            if($list[$i]['company_id']){
                $a[$i] = $this->update($list[$i]['id'], $list[$i]['company_id']);
                if(!$a[$i]){
                    return ['msg' => $i];
                }
            }
        }
        return ['msg' => 'ok'];
    }
    public function getCategory($domain_id)
    {
        $result = User::find()
                ->select(['company_categroy_id'])
                ->where(['id' => $domain_id])
                ->asArray()
                ->one();
        if(!$result){
            return false;
        }
        return $result['company_categroy_id'];
    }
    public function update($id, $company_categroy_id)
    {
        $result = Question::find()
        ->where(['question_id' => $id])
        ->one();
        $result->company_id = $company_categroy_id;
        if(!$result->save()){
            return false;
        }
        return true;
    }

}