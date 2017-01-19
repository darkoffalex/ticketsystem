<?php

use yii\db\Migration;

/**
 * Handles adding bot_fields to table `user`.
 */
class m170117_191256_add_bot_fields_column_to_user_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->addColumn('user', 'bot_key', $this->string());
        $this->addColumn('user', 'bot_user_id', $this->text());
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropColumn('user', 'bot_key');
        $this->dropColumn('user', 'bot_user_id');
    }
}
