<?php

namespace common\models\base;

use Yii;

/**
 * This is the model class for table "order_status".
 *
 * @property int $id
 * @property int $oid
 * @property string $status
 * @property int $created_at
 */
class OrderStatus extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'order_status';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['oid', 'created_at'], 'integer'],
            [['status'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'oid' => 'Oid',
            'status' => 'Status',
            'created_at' => 'Created At',
        ];
    }
}
