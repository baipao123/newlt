<?php

namespace common\models\base;

use Yii;

/**
 * This is the model class for table "order_notify".
 *
 * @property int $id
 * @property string $out_trade_no
 * @property string $params
 * @property int $status
 * @property int $created_at
 */
class OrderNotify extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'order_notify';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['params'], 'required'],
            [['params'], 'string'],
            [['status', 'created_at'], 'integer'],
            [['out_trade_no'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'out_trade_no' => 'Out Trade No',
            'params' => 'Params',
            'status' => 'Status',
            'created_at' => 'Created At',
        ];
    }
}
