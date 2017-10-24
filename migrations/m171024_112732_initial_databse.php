<?php

use yii\db\Migration;

/**
 * Class m171024_112732_initial_databse
 */
class m171024_112732_initial_databse extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->createTable('users', [
            'id' => $this->primaryKey(),
            'username' => $this->string('50')->notNull()->unique(),
            'balance' => $this->double()->defaultValue(0)
        ]);
        $this->createTable('transactions', [
            'id' => $this->primaryKey(),
            'sender_id' => $this->integer()->notNull()->comment('sender user id'),
            'recipient_id' => $this->integer()->notNull()->comment('recipient user id'),
            'cost' => $this->double()->notNull(),
            'created_dt' => $this->dateTime()->defaultExpression('CURRENT_TIMESTAMP')
        ]);
        $this->addForeignKey('transaction_sender_id', 'transactions', 'sender_id', 'users', 'id');
        $this->addForeignKey('transaction_recipient_id', 'transactions', 'recipient_id', 'users', 'id');

    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropTable('users');

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m171024_112732_initial_databse cannot be reverted.\n";

        return false;
    }
    */
}
