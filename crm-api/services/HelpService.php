<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/2/2
 * Time: 13:52
 */
namespace app\services;
use app\foundation\Service;
use app\models\OffHelp;
use app\models\OffHelpAdvice;
use app\models\OffHelpComment;

class HelpService extends Service
{
    public function getFeed($type)
    {
        $res = OffHelp::find()
            ->select('id,content')
            ->where(['id'=>$type])
            ->asArray()
            ->one();
        if (!$res){
            return false;
        }
        return $res;
    }
    /**
     * @param $type_id
     * @param $user_id
     * @param $advice
     * @return bool
     * 用户反馈
     */
    public function submitAdvice($type_id, $user_id, $advice)
    {
        $back = new OffHelpAdvice();
        $back->type = $type_id;
        $back->user_id = $user_id;
        $back->advice = $advice;
        $back->time = time();
        if ($back->save()){
            return true;
        }else{
            return false;
        }
    }

    /**
     * @param $content_id
     * @param $user_id
     * @param $type
     * @return bool
     * 评价帮助详情
     */
    public function evaluateHelp($content_id, $user_id,$type)
    {
        $res = OffHelpComment::find()
            ->where(['content_id'=>$content_id])
            ->andWhere(['user_id'=>$user_id])
            ->asArray()
            ->all();
        if ($res)
        {
            $this->setError('已经评价过！');
            return false;
        }else{
            $comment = new OffHelpComment();
            $comment->user_id = $user_id;
            $comment->content_id = $content_id;
            $comment->type = $type;
            $comment->time = time();
            if ($comment->save()){
                $help = OffHelp::find()
                    ->where(['id'=>$content_id])
                    ->one();
                if ($type == 1){
                    $help->updateCounters(['sumup'=>1]);  //自增总数
                    return ['sumup'=>$help->sumup, 'type'=>1];
                }elseif ($type == 2){
                    $help->updateCounters(['sumdown'=>1]);  //自增总数
                    return ['sumdown'=>$help->sumdown, 'type'=>2];
                }
            }else{
                return false;
            }
        }
    }
    /**
     * @param $list
     * @return array|\yii\db\ActiveRecord[]
     * 获取帮助详情
     */
    public function getDetail($user_id, $list)
    {
        $content = OffHelp::find()
            ->select('id, content, sumup, sumdown')
            ->where(['type'=>0])
            ->andWhere(['parent_id'=>0])
            ->andWhere(['son_id'=>$list])
            ->asArray()
            ->one();
        $title= OffHelp::find()
            ->select('content as title')
            ->where(['id'=>$list])
            ->asArray()
            ->one();
        $result = array_merge($title, $content);

        $res = OffHelpComment::find()
            ->where(['content_id'=>$content['id']])
            ->andWhere(['user_id'=>$user_id])
            ->asArray()
            ->one();
        if ($res){
            $result['type'] = $res['type'];
            $result['flag'] = 1;
        }else{
            $result['flag'] = 0;
        }
        return $result;
    }

    /**
     * 获取帮助列表
     * @param $type
     * @return array|\yii\db\ActiveRecord[]
     */
    public function getList($type)
    {
        $result = OffHelp::find()
            ->select('id, content')
            ->where(['type'=>0])
            ->andWhere(['parent_id'=>$type])
            ->andWhere(['son_id'=>0])
            ->asArray()
            ->all();
        $title= OffHelp::find()
            ->select('content as title')
            ->where(['id'=>$type])
            ->asArray()
            ->one();
        $res = $title;
        $res['content'] = $result;
        return $res;
    }

    /**
     * @return array
     * 获取帮助首页的类型显示
     */
    public function getType()
    {
        $atttion = OffHelp::find()
            ->select('id,content')
            ->where(['type'=>1])
            ->orderBy('id asc')
            ->asArray()
            ->all();
        $notice = OffHelp::find()
            ->select('id,content')
            ->where(['type'=>2])
            ->orderBy('id asc')
            ->asArray()
            ->all();
        return ['atttion'=>$atttion, 'notice'=>$notice] ;
    }
}