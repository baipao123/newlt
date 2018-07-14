<?php

namespace common\models\base;

use Yii;

/**
 * This is the model class for table "{{%user_question_type}}".
 *
 * @property int $id
 * @property int $uid
 * @property int $tid
 * @property int $expire_at
 * @property int $oid
 * @property int $status
 * @property int $created_at
 */
class UserQuestionType extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%user_question_type}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['uid', 'tid', 'expire_at', 'oid', 'created_at'], 'integer'],
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
            'oid' => 'Oid',
            'created_at' => 'Created At',
        ];
    }
}
