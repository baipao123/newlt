<?php

namespace common\models\base;

use Yii;

/**
 * This is the model class for table "question_type".
 *
 * @property int $id
 * @property int $parentId
 * @property string $name
 * @property string $icon
 * @property int $status
 * @property string $setting
 * @property int $created_at
 * @property int $sort
 * @property int $updated_at
 */
class QuestionType extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'question_type';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['parentId', 'created_at', 'status', 'sort', 'updated_at'], 'integer'],
            [['name', 'icon'], 'string', 'max' => 255],
            [['setting'], 'string', 'max' => 2000],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'parentId' => 'Parent ID',
            'name' => 'Name',
            'icon' => 'Icon',
            'status' => 'Status',
            'setting' => 'Setting',
            'created_at' => 'Created At',
            'sort' => 'Sort',
            'updated_at' => 'Updated At',
        ];
    }
}
