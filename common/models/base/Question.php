<?php

namespace common\models\base;

use Yii;

/**
 * This is the model class for table "question".
 *
 * @property int $id
 * @property int $tid
 * @property int $type
 * @property int $parentId
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
 * @property int $success_num
 * @property int $fail_num
 * @property int $view_num
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
        return 'question';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['tid', 'status', 'type', 'parentId', 'difficulty', 'success_num', 'fail_num', 'view_num', 'created_at', 'updated_at'], 'integer'],
//            [['title', 'attaches', 'a', 'aImg', 'b', 'bImg', 'c', 'cImg', 'd', 'dImg', 'e', 'eImg', 'description', 'knowledge'], 'required'],
            [['title', 'attaches', 'a', 'aImg', 'b', 'bImg', 'c', 'cImg', 'd', 'dImg', 'e', 'eImg', 'answer','description', 'knowledge'], 'string'],
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
            'parentId' => 'Parent ID',
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
            'success_num' => 'Success Num',
            'fail_num' => 'Fail Num',
            'view_num' => 'View Num',
            'status' => 'Status',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }
}
