<?php

use yii\db\Migration;

/**
 * Handles adding author to table `ticket`.
 * Has foreign keys to the tables:
 *
 * - `user`
 */
class m170202_153738_add_author_column_to_ticket_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->addColumn('ticket', 'author_id', $this->integer());

        // creates index for column `author_id`
        $this->createIndex(
            'idx-ticket-author_id',
            'ticket',
            'author_id'
        );

        // add foreign key for table `user`
        $this->addForeignKey(
            'fk-ticket-author_id',
            'ticket',
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
        // drops foreign key for table `user`
        $this->dropForeignKey(
            'fk-ticket-author_id',
            'ticket'
        );

        // drops index for column `author_id`
        $this->dropIndex(
            'idx-ticket-author_id',
            'ticket'
        );

        $this->dropColumn('ticket', 'author_id');
    }
}
