<?php

namespace app\models;

use Yii;


class Examine extends \yii\db\ActiveRecord
{
	public static function tableName()
    {
        return '{{%examine}}';
    }
    //关联表      get(关联表Model名)
    public function getUser(){
        //参数一 关联Model名   参数二 关联字段 不能写表.t_id 自己默认后边是本Model的表id  前边是关联表的id
        return $this->hasOne(User::className(),['id'=>'uid']);
    }
    public function getPetition(){
        //参数一 关联Model名   参数二 关联字段 不能写表.t_id 自己默认后边是本Model的表id  前边是关联表的id
        return $this->hasOne(Petition::className(),['id'=>'petition_id']);
    }
    public static function getDb()
    {
        return Yii::$app->get('dbofficial');
    }
    // public function rules()
    // {
    //     return [
    //         [['problem_title', 'problem_content', 'priority', 'create_time', 'user_id', 'user_name', 'update_time', 'area', 'city', 'department', 'company_id'], 'required'],
    //         [['priority', 'create_time', 'user_id', 'problem_lock', 'update_time', 'company_id'], 'integer'],
    //         [['problem_title', 'collaboration_department'], 'string', 'max' => 200],
    //         [['problem_content'], 'string', 'max' => 2000],
    //         [['user_name', 'area', 'city', 'department'], 'string', 'max' => 100]
    //     ];
    // }
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'petition_id' => Yii::t('app', '签呈ID'),
            'uid' => Yii::t('app', '用户ID'),
            'status' => Yii::t('app', '签呈状态 0：不同意 1：同意'),
            'advice' => Yii::t('app', '签呈意见'),
            'examine_time'=>Yii::t('app','审批时间'),
            'is_visible'=>Yii::t('app','是否显示'),
        ];
    }
}