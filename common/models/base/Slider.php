<?php

namespace common\models\base;

use Yii;

/**
 * This is the model class for table "slider".
 *
 * @property int $id
 * @property string $title
 * @property int $type
 * @property int $tid
 * @property string $cover
 * @property string $link
 * @property int sort
 * @property int $status
 * @property int $start_at
 * @property int $end_at
 * @property int $created_at
 * @property int $aid
 * @property int $updated_at
 */
class Slider extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'slider';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['tid', 'type', 'sort', 'status', 'start_at', 'end_at', 'created_at', 'aid', 'updated_at'], 'integer'],
            [['title', 'cover', 'link'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'title' => 'Title',
            'type' => 'Type',
            'tid' => 'Tid',
            'cover' => 'Cover',
            'link' => 'Link',
            'sort' => 'Sort',
            'status' => 'Status',
            'start_at' => 'Start At',
            'end_at' => 'End At',
            'created_at' => 'Created At',
            'aid' => 'Aid',
            'updated_at' => 'Updated At',
        ];
    }
}
