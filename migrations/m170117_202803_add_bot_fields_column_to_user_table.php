<?php

use yii\db\Migration;

/**
 * Handles adding bot_fields to table `user`.
 */
class m170117_202803_add_bot_fields_column_to_user_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->addColumn('user', 'bot_notify_settings', $this->string());
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropColumn('user', 'bot_notify_settings');
    }
}
