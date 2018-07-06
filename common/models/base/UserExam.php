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
 * @property int $success_num
 * @property int $fail_num
 * @property int $created_at
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
            [['uid', 'tid', 'expire_at', 'score', 'success_num', 'fail_num', 'created_at'], 'integer'],
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
            'success_num' => 'Success Num',
            'fail_num' => 'Fail Num',
            'created_at' => 'Created At',
        ];
    }
}
