<?php

namespace common\models\base;

use Yii;

/**
 * This is the model class for table "{{%user_exam_question}}".
 *
 * @property int $id
 * @property int $uid
 * @property int $tid
 * @property int $eid
 * @property int $qid
 * @property string $userAnswer
 * @property string $answer
 * @property string $score
 * @property int $status
 * @property int $created_at
 * @property int $updated_at
 */
class UserExamQuestion extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%user_exam_question}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['uid', 'tid', 'eid', 'qid', 'status', 'created_at', 'updated_at'], 'integer'],
            [['userAnswer', 'answer', 'score'], 'string', 'max' => 255],
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
            'eid' => 'Eid',
            'qid' => 'Qid',
            'userAnswer' => 'User Answer',
            'answer' => 'Answer',
            'score' => 'Score',
            'status' => 'Status',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }
}
