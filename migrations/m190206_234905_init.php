<?php

use yii\db\Migration;

/**
 * Class m190206_234905_init
 */
class m190206_234905_init extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%category}}', [
            'id' => $this->primaryKey(),
            'parent_id' => $this->integer()->defaultValue(null),
            'name' => $this->string(),
        ], $tableOptions);
        $this->addForeignKey('fk_category_parent_id',
            '{{%category}}', 'parent_id',
            '{{%category}}', 'id',
            'CASCADE', 'CASCADE'
        );

        $this->createTable('{{%offer}}', [
            'id' => $this->primaryKey(),
            'own_category_id' => $this->integer(),
            'available' => $this->boolean()->notNull()->defaultValue(false),
            'url' => $this->string(),
            'pictures' => $this->json(),
            'price' => $this->integer()->defaultValue(0)->comment('price value as minimal units'),
            'currency' => $this->string()->notNull()->defaultValue('EUR'),
            'name' => $this->string()->notNull(),
            'description' => $this->text(),
            'params' => $this->json(),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer(),
        ], $tableOptions);
        $this->createIndex('idx_category__parent_id', '{{%offer}}', 'name');
        $this->addForeignKey('fk_offer__own_category_id',
            '{{%offer}}', 'own_category_id',
            '{{%category}}', 'id',
            'CASCADE', 'CASCADE'
        );

        $this->createTable('{{%offer_category}}', [
            'offer_id' => $this->integer()->notNull(),
            'category_id' => $this->integer()->notNull(),
        ], $tableOptions);
        $this->addForeignKey('fk_offer_category__category_id',
            '{{%offer_category}}', 'category_id',
            '{{%category}}', 'id',
            'CASCADE', 'CASCADE'
        );
        $this->addForeignKey('fk_offer_category__offer_id',
            '{{%offer_category}}', 'offer_id',
            '{{%offer}}', 'id',
            'CASCADE', 'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%offer_category}}');

        $this->dropTable('{{%offer}}');

        $this->dropTable('{{%category}}');
    }

}
