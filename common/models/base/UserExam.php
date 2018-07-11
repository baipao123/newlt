<?php

namespace common\models\base;

use Yii;

/**
 * This is the model class for table "{{%user_exam}}".
 *
 * @property int $id
 * @property int $uid
 * @property int $tid
 * @property int $expire_at
 * @property int $score
 * @property int $status
 * @property string $qIds
 * @property int $created_at
 * @property int $finish_at
 * @property string $detail
 */
class UserExam extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%user_exam}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['uid', 'tid', 'expire_at', 'score', 'status', 'created_at', 'finish_at'], 'integer'],
            [['qIds','detail'], 'string'],
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
            'expire_at' => 'Expire At',
            'score' => 'Score',
            'status' => 'Status',
            'qIds' => 'Q Ids',
            'created_at' => 'Created At',
            'finish_at' => 'Finish At',
            'detail' => 'Detail',
        ];
    }
}
