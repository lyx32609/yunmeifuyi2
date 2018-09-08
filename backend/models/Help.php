<?php

namespace backend\models;

use Symfony\Component\CssSelector\Parser\Handler\HashHandler;
use Yii;

/**
 * This is the model class for table "off_help".
 *
 * @property integer $id
 * @property integer $type
 * @property integer $parent_id
 * @property integer $son_id
 * @property string $content
 * @property integer $sumup
 * @property integer $sumdown
 */
class Help extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'off_help';
    }

    /**
     * @return \yii\db\Connection the database connection used by this AR class.
     */
    public static function getDb()
    {
        return Yii::$app->get('dbofficial');
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['parent_id'], 'required'],
            [['type', 'son_id', 'sumup', 'sumdown'], 'integer'],
            [['content'], 'string', 'max' => 65535],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'type' => '类别',
            'parent_id' => 'parent_id',
            'son_id' => 'son_id',
            'content' => '内容',
            'sumup' => '赞',
            'sumdown' => '踩',
        ];
    }

    /*
     * 获取二级分类
     * */
    public static function findSecond($id){
        return Help::find()->select(['id','parent_id','content','type'])
            ->where( ['parent_id'=>$id,'son_id'=>0,'type'=>0])
            ;
    }
    /*
     * 获取三级分类*/
    public static function findThird($id){
        return Help::find()->select(['id','parent_id','content','type'])
            ->where( ['son_id'=>$id,'parent_id'=>0,'type'=>0])
            ;
    }
    /*
     * 获取非顶级分类
     * */
    public static function findType(){
        return Help::find()->select(['id','parent_id','content','type'])
            ->where(['!=','parent_id',0]);
    }
}
