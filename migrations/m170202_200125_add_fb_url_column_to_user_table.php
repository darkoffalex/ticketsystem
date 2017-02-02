<?php

use yii\db\Migration;

/**
 * Handles adding fb_url to table `user`.
 */
class m170202_200125_add_fb_url_column_to_user_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->addColumn('user', 'fb_profile_url', $this->string());
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropColumn('user', 'fb_profile_url');
    }
}
