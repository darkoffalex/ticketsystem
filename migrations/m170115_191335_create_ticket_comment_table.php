<?php

use yii\db\Migration;

/**
 * Handles the creation of table `ticket_comment`.
 * Has foreign keys to the tables:
 *
 * - `ticket`
 * - `user`
 */
class m170115_191335_create_ticket_comment_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('ticket_comment', [
            'id' => $this->primaryKey(),
            'ticket_id' => $this->integer(),
            'author_id' => $this->integer(),
            'text' => $this->text(),
            'created_at' => $this->dateTime(),
            'updated_at' => $this->dateTime(),
            'created_by_id' => $this->integer(),
            'updated_by_id' => $this->integer(),
        ]);

        // creates index for column `ticket_id`
        $this->createIndex(
            'idx-ticket_comment-ticket_id',
            'ticket_comment',
            'ticket_id'
        );

        // add foreign key for table `ticket`
        $this->addForeignKey(
            'fk-ticket_comment-ticket_id',
            'ticket_comment',
            'ticket_id',
            'ticket',
            'id',
            'CASCADE',
            'NO ACTION'
        );

        // creates index for column `author_id`
        $this->createIndex(
            'idx-ticket_comment-author_id',
            'ticket_comment',
            'author_id'
        );

        // add foreign key for table `user`
        $this->addForeignKey(
            'fk-ticket_comment-author_id',
            'ticket_comment',
            'author_id',
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
        // drops foreign key for table `ticket`
        $this->dropForeignKey(
            'fk-ticket_comment-ticket_id',
            'ticket_comment'
        );

        // drops index for column `ticket_id`
        $this->dropIndex(
            'idx-ticket_comment-ticket_id',
            'ticket_comment'
        );

        // drops foreign key for table `user`
        $this->dropForeignKey(
            'fk-ticket_comment-author_id',
            'ticket_comment'
        );

        // drops index for column `author_id`
        $this->dropIndex(
            'idx-ticket_comment-author_id',
            'ticket_comment'
        );

        $this->dropTable('ticket_comment');
    }
}
