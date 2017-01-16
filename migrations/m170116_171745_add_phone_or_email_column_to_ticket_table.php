<?php

use yii\db\Migration;

/**
 * Handles adding phone_or_email to table `ticket`.
 */
class m170116_171745_add_phone_or_email_column_to_ticket_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->addColumn('ticket', 'phone_or_email', $this->string());
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropColumn('ticket', 'phone_or_email');
    }
}
