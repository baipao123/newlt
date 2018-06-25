<?php

namespace common\models\base;

use Yii;

/**
 * This is the model class for table "{{%user}}".
 *
 * @property int $id
 * @property int $tid
 * @property int $expire_at
 * @property string $openId
 * @property string $unionId
 * @property string $session_key
 * @property string $realname
 * @property string $phone
 * @property string $username
 * @property string $nickname
 * @property int $gender
 * @property string $avatar
 * @property string $cityName
 * @property string $province
 * @property string $country
 * @property int $status
 * @property string $auth_key
 * @property int $real_at
 * @property int $created_at
 * @property int $last_login
 */
class User extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%user}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['tid', 'expire_at', 'real_at', 'created_at', 'last_login'], 'integer'],
            [['openId', 'unionId', 'session_key', 'realname', 'phone', 'username', 'nickname', 'avatar', 'cityName', 'province', 'country', 'auth_key'], 'string', 'max' => 255],
            [['gender', 'status'], 'string', 'max' => 1],
            [['openId'], 'unique'],
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
            'expire_at' => 'Expire At',
            'openId' => 'Open ID',
            'unionId' => 'Union ID',
            'session_key' => 'Session Key',
            'realname' => 'Realname',
            'phone' => 'Phone',
            'username' => 'Username',
            'nickname' => 'Nickname',
            'gender' => 'Gender',
            'avatar' => 'Avatar',
            'cityName' => 'City Name',
            'province' => 'Province',
            'country' => 'Country',
            'status' => 'Status',
            'auth_key' => 'Auth Key',
            'real_at' => 'Real At',
            'created_at' => 'Created At',
            'last_login' => 'Last Login',
        ];
    }
}
