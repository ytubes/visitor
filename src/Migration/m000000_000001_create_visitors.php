<?php
namespace Module\Visitor\Migration;

use Yii;
use yii\db\Migration;

class m000000_000001_create_visitors extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=MyISAM';
        }

        $tableName = 'visitors';
        $tableSchema = Yii::$app->db->schema->getTableSchema($tableName);

        if ($tableSchema === null) {
            $this->createTable($tableName, [
                'ip' => 'varbinary(16) NOT NULL',
                'first_visit' => 'timestamp NULL DEFAULT NULL',
                'last_visit' => 'timestamp NULL DEFAULT NULL',
                'session_time' => 'smallint(5) unsigned NOT NULL DEFAULT 0',
                'raw_in' => 'tinyint(3) unsigned NOT NULL DEFAULT 0',
                'views' => 'tinyint(3) unsigned NOT NULL DEFAULT 0',
                'clicks' => 'tinyint(3) unsigned NOT NULL DEFAULT 0',
                'ref_site' => 'varchar(255) NOT NULL DEFAULT \'\'',
                'ref_group' => "enum('se', 'bookmark', 'internal', 'links', 'other') DEFAULT NULL",
                'device_group' => "enum('desktop', 'tablet', 'mobile') DEFAULT NULL",
            ], $tableOptions);

            $this->createIndex('ip', $tableName, 'ip');
            $this->createIndex('first_visit', $tableName, 'first_visit');

            $tableName = 'cron_jobs';
            $tableSchema = Yii::$app->db->schema->getTableSchema($tableName);

                // add cron job
            if ($tableSchema !== null) {
                $this->insert($tableName, [
                    'module' => 'visitor',
                    'handler_class' => Module\Visitor\Cron\Job\ClearOldVisitorStatJob::class,
                    'cron_expression' => '* * * * *',
                    'priority' => 1000,
                    'enabled' => 1
                ]);
            }
        }
    }

    public function down()
    {
        echo "m170629_023626_add_visitor cannot be reverted.\n";

        return false;
    }
}
