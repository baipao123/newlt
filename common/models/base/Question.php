<?php

namespace common\models\base;

use Yii;

/**
 * This is the model class for table "{{%question}}".
 *
 * @property int $id
 * @property int $tid
 * @property int $type
 * @property string $title
 * @property string $attaches
 * @property string $a
 * @property string $aImg
 * @property string $b
 * @property string $bImg
 * @property string $c
 * @property string $cImg
 * @property string $d
 * @property string $dImg
 * @property string $e
 * @property string $eImg
 * @property string $answer
 * @property string $description
 * @property string $knowledge
 * @property int $difficulty
 * @property int $view_num
 * @property int $success_num
 * @property int $fail_num
 * @property int $status
 * @property int $created_at
 * @property int $updated_at
 */
class Question extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%question}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['tid', 'type', 'difficulty','view_num', 'success_num', 'fail_num', 'status', 'created_at', 'updated_at'], 'integer'],
            //            [['title', 'attaches', 'a', 'aImg', 'b', 'bImg', 'c', 'cImg', 'd', 'dImg', 'description', 'knowledge'], 'required'],
            [['title', 'attaches', 'a', 'aImg', 'b', 'bImg', 'c', 'cImg', 'd', 'dImg', 'e', 'eImg', 'description', 'knowledge'], 'string'],
            [['answer'], 'string', 'max' => 255],
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
            'type' => 'Type',
            'title' => 'Title',
            'attaches' => 'Attaches',
            'a' => 'A',
            'aImg' => 'A Img',
            'b' => 'B',
            'bImg' => 'B Img',
            'c' => 'C',
            'cImg' => 'C Img',
            'd' => 'D',
            'dImg' => 'D Img',
            'e' => 'E',
            'eImg' => 'E Img',
            'answer' => 'Answer',
            'description' => 'Description',
            'knowledge' => 'Knowledge',
            'difficulty' => 'Difficulty',
            'view_num' => 'View Num',
            'success_num' => 'Success Num',
            'fail_num' => 'Fail Num',
            'status' => 'Status',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }
}
