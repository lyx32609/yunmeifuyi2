<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "off_menus".
 *
 * @property string $id
 * @property string $name
 * @property string $url
 * @property string $parent_id
 * @property string $sort
 * @property string $icon_class
 * @property integer $status
 */
class Menus extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'off_menus';
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
            [['name', 'url', 'status'], 'required'],
            [['parent_id', 'sort', 'status'], 'integer'],
            [['name'], 'string', 'max' => 50],
            [['url'], 'string', 'max' => 255],
            [['icon_class'], 'string', 'max' => 100],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'name' => Yii::t('app', 'Name'),
            'url' => Yii::t('app', 'Url'),
            'parent_id' => Yii::t('app', 'Parent ID'),
            'sort' => Yii::t('app', '排序  越大越后'),
            'icon_class' => Yii::t('app', '图标class样式'),
            'status' => Yii::t('app', 'Status'),
        ];
    }
    
    public static  function findMenu(){
        $id = Yii::$app->user->identity->id;
        //判断权限   一线员工没有配置权限
        if(!in_array($id, Yii::$app->params['through']))
        {
            $userRoute = UserRoute::find()
            ->where(['userid'=>$id])
            ->asArray()
            ->one();
            $ids = explode(',', $userRoute['menuids']);
            $rank = Yii::$app->user->identity->rank;
            if($rank == 1 || $rank == 2 || $rank == 4)
            { //一线员工没有权限
                $where = ['id'=>0];
            }else
            {
                $where = ['id'=>$ids];
            }
        }
        else
        {
            $where = '';
        }
        return Menus::find()
        ->where($where)
        ->andWhere(['switch'=>1]);
    }
    
}
