<?php
namespace RS\Visitor\Model;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "visitors".
 *
 * @property resource $ip
 * @property string $first_visit
 * @property string $last_visit
 * @property integer $session_time
 * @property integer $raw_in
 * @property integer $views
 * @property integer $clicks
 * @property string $ref_site
 * @property string $ref_group
 * @property string $device_group
 */
class Visitor extends ActiveRecord
{
    public $visitNum;

    /**
     * @var const группы устройств
     */
    const DEVICE_DESKTOP = 'desktop';
    const DEVICE_MOBILE = 'mobile';

    /**
     * @var const группы реферов
     */
    const REF_GROUP_SE = 'se';
    const REF_GROUP_BOOKMARK = 'bookmark';
    const REF_GROUP_LINKS = 'links';
    const REF_GROUP_INTERNAL = 'internal';

    /**
     * @var string[]
     */
    private static $deviceGroups = [
        self::DEVICE_DESKTOP,
        self::DEVICE_MOBILE,
    ];

    /**
     * @var string[]
     */
    private static $refererGroups = [
        self::REF_GROUP_SE,
        self::REF_GROUP_BOOKMARK,
        self::REF_GROUP_LINKS,
        self::REF_GROUP_INTERNAL,
    ];

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'visitors';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['ip'], 'required'],
            [['first_visit', 'last_visit'], 'safe'],
            [['session_time', 'raw_in', 'views', 'clicks'], 'integer'],
            [['ref_group', 'device_group'], 'string'],
            [['ip'], 'string', 'max' => 16],
            [['ref_site'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'ip' => 'Ip',
            'first_visit' => 'First Visit',
            'last_visit' => 'Last Visit',
            'session_time' => 'Session Time',
            'raw_in' => 'Raw In',
            'views' => 'Views',
            'clicks' => 'Clicks',
            'ref_site' => 'Referer Site',
            'ref_group' => 'Referer group',
            'device_group' => 'Device group',
        ];
    }

    public static function findByIp($ip)
    {
        $packedIp = inet_pton($ip);

        return self::find()
            ->where(['ip' => $packedIp])
            ->all();
    }

    /**
     * После поиска автоматически преобразует IP пользователя в читаемый формат.
     */
    public function afterFind()
    {
         $ip = @inet_ntop($this->ip);

         if (false !== $ip) {
             $this->ip = $ip;
         }

        return parent::afterFind();
    }

    /**
     * Перед записью в базу автоматически конвертирует IP адрес пользователя в упакованное in_addr представление
     */
    public function beforeSave($insert)
    {
        if (!parent::beforeSave($insert)) {
            return false;
        }

        $this->ip = inet_pton($this->ip);

        return true;
    }

    /**
     * @return array
     */
    public static function getDeviceGroups()
    {
        return self::$deviceGroups;
    }

    /**
     * @return array
     */
    public static function getRefererGroups()
    {
        return self::$refererGroups;
    }
}
