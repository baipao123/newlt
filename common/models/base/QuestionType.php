<?php

namespace common\models\base;

use Yii;

/**
 * This is the model class for table "question_type".
 *
 * @property int $id
 * @property int $tid
 * @property int $parentId
 * @property string $name
 * @property string $icon
 * @property int $status
 * @property int $sort
 * @property string $description
 * @property int $examNum
 * @property int $score
 * @property int $time
 * @property int $created_at
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
            [['tid', 'parentId', 'sort', 'examNum', 'score', 'time', 'created_at', 'updated_at'], 'integer'],
            [['name', 'icon', 'description'], 'string', 'max' => 255],
            [['status'], 'string', 'max' => 1],
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
            'parentId' => 'Parent ID',
            'name' => 'Name',
            'icon' => 'Icon',
            'status' => 'Status',
            'sort' => 'Sort',
            'description' => 'Description',
            'examNum' => 'Exam Num',
            'score' => 'Score',
            'time' => 'Time',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }
}
