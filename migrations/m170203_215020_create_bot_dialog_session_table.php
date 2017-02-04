<?php

use yii\db\Migration;

/**
 * Handles the creation of table `bot_dialog_session`.
 * Has foreign keys to the tables:
 *
 * - `user`
 */
class m170203_215020_create_bot_dialog_session_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('bot_dialog_session', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer(),
            'user_msg_text' => $this->text(),
            'user_msg_type' => $this->integer(),
            'operable_ticket_id' => $this->integer(),
            'life_time' => $this->integer(),
            'created_at' => $this->dateTime(),
            'updated_at' => $this->dateTime(),
        ]);

        // creates index for column `user_id`
        $this->createIndex(
            'idx-bot_dialog_session-user_id',
            'bot_dialog_session',
            'user_id'
        );

        // add foreign key for table `user`
        $this->addForeignKey(
            'fk-bot_dialog_session-user_id',
            'bot_dialog_session',
            'user_id',
            'user',
            'id',
            'CASCADE',
            'NO ACTION'
        );
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        // drops foreign key for table `user`
        $this->dropForeignKey(
            'fk-bot_dialog_session-user_id',
            'bot_dialog_session'
        );

        // drops index for column `user_id`
        $this->dropIndex(
            'idx-bot_dialog_session-user_id',
            'bot_dialog_session'
        );

        $this->dropTable('bot_dialog_session');
    }
}
