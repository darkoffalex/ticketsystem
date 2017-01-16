<?php

use yii\db\Migration;

/**
 * Handles the creation of table `ticket_image`.
 * Has foreign keys to the tables:
 *
 * - `ticket`
 */
class m170115_174115_create_ticket_image_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('ticket_image', [
            'id' => $this->primaryKey(),
            'ticket_id' => $this->integer(),
            'name' => $this->string(),
            'filename' => $this->string(),
            'original_filename' => $this->string(),
            'mime_type' => $this->string(),
            'priority' => $this->integer(),
            'created_at' => $this->dateTime(),
            'updated_at' => $this->dateTime(),
            'created_by_id' => $this->integer(),
            'updated_by_id' => $this->integer(),
        ]);

        // creates index for column `ticket_id`
        $this->createIndex(
            'idx-ticket_image-ticket_id',
            'ticket_image',
            'ticket_id'
        );

        // add foreign key for table `ticket`
        $this->addForeignKey(
            'fk-ticket_image-ticket_id',
            'ticket_image',
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
        // drops foreign key for table `ticket`
        $this->dropForeignKey(
            'fk-ticket_image-ticket_id',
            'ticket_image'
        );

        // drops index for column `ticket_id`
        $this->dropIndex(
            'idx-ticket_image-ticket_id',
            'ticket_image'
        );

        $this->dropTable('ticket_image');
    }
}
