<?php

use yii\db\Migration;

/**
 * Handles adding bot_msg to table `user`.
 */
class m170203_211113_add_bot_msg_column_to_user_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->addColumn('user', 'last_bot_message_type', $this->integer());
        $this->addColumn('user', 'last_bot_operable_ticket',$this->integer());
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropColumn('user', 'last_bot_message_type');
        $this->dropColumn('user', 'last_bot_operable_ticket');
    }
}
