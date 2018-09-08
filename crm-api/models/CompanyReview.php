<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%company_review}}".
 *
 * @property string $id
 * @property integer $review
 */
class CompanyReview extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%company_review}}';
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
            [['review'], 'required'],
            [['review'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'review' => Yii::t('app', 'Review'),
        ];
    }
}
