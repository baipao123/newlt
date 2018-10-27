<?php

namespace common\models\base;

use Yii;

/**
 * This is the model class for table "user_exam".
 *
 * @property int $id
 * @property int $uid
 * @property int $tid
 * @property int $num
 * @property int $totalNum
 * @property int $passNum
 * @property int $errNum
 * @property int $expire_at
 * @property int $score
 * @property int $status
 * @property int $created_at
 * @property int $finish_at
 */
class UserExam extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user_exam';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['uid', 'tid', 'num', 'totalNum', 'passNum', 'errNum', 'expire_at', 'score', 'status', 'created_at', 'finish_at'], 'integer'],
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
            'num' => 'Num',
            'totalNum' => 'Total Num',
            'passNum' => 'Pass Num',
            'errNum' => 'Err Num',
            'expire_at' => 'Expire At',
            'score' => 'Score',
            'status' => 'Status',
            'created_at' => 'Created At',
            'finish_at' => 'Finish At',
        ];
    }
}
