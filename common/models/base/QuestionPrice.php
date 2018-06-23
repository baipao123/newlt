<?php

namespace common\models\base;

use Yii;

/**
 * This is the model class for table "question_price".
 *
 * @property int $id
 * @property int $tid
 * @property string $title
 * @property string $cover
 * @property int $price
 * @property int $oldPrice
 * @property int $type
 * @property int $hour
 * @property int $start_at
 * @property int $end_at
 * @property int $status
 * @property string $note
 * @property int $sort
 * @property int $created_at
 * @property int $updated_at
 */
class QuestionPrice extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'question_price';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['tid', 'price', 'oldPrice', 'type', 'hour', 'start_at', 'end_at', 'status', 'sort', 'created_at', 'updated_at'], 'integer'],
            [['note'], 'string'],
            [['title', 'cover'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'tid' => 'Tid',
            'title' => 'Title',
            'cover' => 'Cover',
            'price' => 'Price',
            'oldPrice' => 'Old Price',
            'type' => 'Type',
            'hour' => 'Hour',
            'start_at' => 'Start At',
            'end_at' => 'End At',
            'status' => 'Status',
            'note' => 'Note',
            'sort' => 'Sort',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }
}
