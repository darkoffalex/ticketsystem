<?php

use yii\db\Migration;

/**
 * Handles the creation of table `ticket`.
 * Has foreign keys to the tables:
 *
 * - `user`
 */
class m170115_172638_create_ticket_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('ticket', [
            'id' => $this->primaryKey(),
            'performer_id' => $this->integer(),
            'status_id' => $this->integer(),
            'type_id' => $this->integer(),
            'category_id' => $this->integer(),
            'author_name' => $this->string(),
            'text' => $this->text(),
            'link' => $this->text(),
            'log' => $this->text(),
            'created_at' => $this->dateTime(),
            'updated_at' => $this->dateTime(),
            'created_by_id' => $this->integer(),
            'updated_by_id' => $this->integer(),
        ]);

        // creates index for column `performer_id`
        $this->createIndex(
            'idx-ticket-performer_id',
            'ticket',
            'performer_id'
        );

        // add foreign key for table `user`
        $this->addForeignKey(
            'fk-ticket-performer_id',
            'ticket',
            'performer_id',
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
            'fk-ticket-performer_id',
            'ticket'
        );

        // drops index for column `performer_id`
        $this->dropIndex(
            'idx-ticket-performer_id',
            'ticket'
        );

        $this->dropTable('ticket');
    }
}
