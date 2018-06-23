<?php

namespace common\models\base;

use Yii;

/**
 * This is the model class for table "order".
 *
 * @property int $id
 * @property int $uid
 * @property int $tid
 * @property int $pid
 * @property string $openId
 * @property string $formId
 * @property string $title
 * @property string $cover
 * @property int $price
 * @property int $hour
 * @property string $out_trade_no
 * @property string $prepay_id
 * @property string $trade_no
 * @property int $payat
 * @property int $status
 * @property int $paytime
 * @property int $created_at
 * @property int $updated_at
 */
class Order extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'order';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['uid', 'tid', 'pid', 'price', 'hour', 'payat', 'paytime', 'status', 'created_at', 'updated_at'], 'integer'],
            [['openId', 'formId', 'title', 'cover', 'prepay_id'], 'string', 'max' => 255],
            [['out_trade_no', 'trade_no'], 'string', 'max' => 32],
            [['out_trade_no'], 'unique'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'uid' => 'Uid',
            'tid' => 'Tid',
            'pid' => 'Pid',
            'openId' => 'Open ID',
            'formId' => 'Form ID',
            'title' => 'Title',
            'cover' => 'Cover',
            'price' => 'Price',
            'hour' => 'Hour',
            'out_trade_no' => 'Out Trade No',
            'prepay_id' => 'Prepay ID',
            'trade_no' => 'Trade No',
            'payat' => 'Payat',
            'status' => 'Status',
            'paytime' => 'Paytime',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }
}
