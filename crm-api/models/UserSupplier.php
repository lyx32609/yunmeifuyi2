<?php
namespace app\models;

use Yii;

use app\models\Supplier;
/**
 * This is the model class for table "off_user_supplier".
 *
 * @property integer $id
 * @property string $user_id
 * @property string $supplier_id
 *
 * @property User $user
 */
class UserSupplier extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'off_user_supplier';
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
            [['user_id', 'supplier_id'], 'required'],
            [['user_id', 'supplier_id'], 'integer']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'user_id' => Yii::t('app', 'User ID'),
            'supplier_id' => Yii::t('app', 'Supplier ID'),
        ];
    }


}
