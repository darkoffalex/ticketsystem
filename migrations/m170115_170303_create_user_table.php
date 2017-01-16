<?php

use yii\db\Migration;

/**
 * Handles the creation of table `user`.
 */
class m170115_170303_create_user_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('user', [
            'id' => $this->primaryKey(),
            'fb_id' => $this->text(),
            'fb_avatar_url' => $this->text(),
            'username' => $this->string(),
            'auth_key' => $this->text(),
            'password_hash' => $this->text(),
            'password_reset_token' => $this->text(),
            'name' => $this->string(),
            'surname' => $this->string(),
            'email' => $this->string(),
            'role_id' => $this->integer(),
            'status_id' => $this->integer(),
            'created_at' => $this->dateTime(),
            'updated_at' => $this->dateTime(),
            'online_at' => $this->dateTime(),
            'created_by_id' => $this->integer(),
            'updated_by_id' => $this->integer(),
        ]);

        $this->insert('user',[
            'username' => 'admin',
            'auth_key' => Yii::$app->security->generateRandomString(),
            'password_hash' => Yii::$app->security->generatePasswordHash('1234'),
            'name' => 'Валерий',
            'surname' => 'Гатальский',
            'role_id' => \app\helpers\Constants::ROLE_ADMIN,
            'status_id' => \app\helpers\Constants::STATUS_ENABLED,
            'created_at' => date('Y-m-d H:i:s',time()),
            'updated_at' => date('Y-m-d H:i:s',time()),
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('user');
    }
}
