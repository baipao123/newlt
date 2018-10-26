<?php

namespace common\models\base;

use Yii;

/**
 * This is the model class for table "user_train_record".
 *
 * @property int $uid
 * @property int $tid
 * @property int $offset
 * @property int $last_at
 */
class UserTrainRecord extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user_train_record';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['uid', 'tid'], 'required'],
            [['uid', 'tid', 'offset', 'last_at'], 'integer'],
            [['uid', 'tid'], 'unique', 'targetAttribute' => ['uid', 'tid']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'uid' => 'Uid',
            'tid' => 'Tid',
            'offset' => 'Offset',
            'last_at' => 'Last At',
        ];
    }
}
