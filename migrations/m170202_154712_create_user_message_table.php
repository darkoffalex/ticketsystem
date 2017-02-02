<?php

use yii\db\Migration;

/**
 * Handles the creation of table `user_message`.
 * Has foreign keys to the tables:
 *
 * - `user`
 * - `ticket`
 */
class m170202_154712_create_user_message_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('user_message', [
            'id' => $this->primaryKey(),
            'message' => $this->text(),
            'author_id' => $this->integer(),
            'ticket_id' => $this->integer(),
            'created_at' => $this->dateTime(),
            'updated_at' => $this->dateTime(),
            'created_by_id' => $this->integer(),
            'updated_by_id' => $this->integer(),
        ]);

        // creates index for column `author_id`
        $this->createIndex(
            'idx-user_message-author_id',
            'user_message',
            'author_id'
        );

        // add foreign key for table `user`
        $this->addForeignKey(
            'fk-user_message-author_id',
            'user_message',
            'author_id',
            'user',
            'id',
            'CASCADE',
            'NO ACTION'
        );

        // creates index for column `ticket_id`
        $this->createIndex(
            'idx-user_message-ticket_id',
            'user_message',
            'ticket_id'
        );

        // add foreign key for table `ticket`
        $this->addForeignKey(
            'fk-user_message-ticket_id',
            'user_message',
            'ticket_id',
            'ticket',
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
            'fk-user_message-author_id',
            'user_message'
        );

        // drops index for column `author_id`
        $this->dropIndex(
            'idx-user_message-author_id',
            'user_message'
        );

        // drops foreign key for table `ticket`
        $this->dropForeignKey(
            'fk-user_message-ticket_id',
            'user_message'
        );

        // drops index for column `ticket_id`
        $this->dropIndex(
            'idx-user_message-ticket_id',
            'user_message'
        );

        $this->dropTable('user_message');
    }
}
