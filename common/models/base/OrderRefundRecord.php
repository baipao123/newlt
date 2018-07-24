<?php

namespace common\models\base;

use Yii;

/**
 * This is the model class for table "order".
 *
 * @property int $id
 * @property string $out_trade_no
 * @property string $trade_no
 * @property string $refund_no
 * @property int $cash
 * @property int $result
 * @property string $params
 * @property string $return
 * @property int $admin_id
 * @property int $created_at
 */
class OrderRefundRecord extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'order_refund_record';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['cash', 'result', 'admin_id', 'created_at'], 'integer'],
            [['out_trade_no', 'trade_no', 'refund_no'], 'string', 'max' => 255],
            [['params', 'return'], 'string'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'id'           => 'ID',
            'out_trade_no' => 'out_trade_no',
            'trade_no'     => 'trade_no',
            'refund_no'    => 'refund_no',
            'cash'         => 'cash',
            'result'       => 'result',
            'params'       => 'params',
            'return'       => 'return',
            'admin_id'     => 'admin_id',
            'created_at'   => 'Created At',
        ];
    }
}
