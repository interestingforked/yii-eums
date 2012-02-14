<?php

class m120214_052246_oauth_table extends CDbMigration
{
	public function up() {
    $this->createTable("{{eums_oauth}}", array(
      'id'=>'pk',
      'user_id'=>'int not null',
      'provider'=>'string not null',
      'key'=>'string not null',
      'secret'=>'string not null',
      'active'=>'int default "1"',
      'date_created'=>'datetime',
    ));
    if (strpos($this->getDbConnection()->getDriverName(), "mysql") !== false) {
      $this->getDbConnection()->createCommand("ALTER TABLE {{eums_oauth}} ENGINE = INNODB")->execute();
      $this->addForeignKey("eums_oauth_userid_fk", "{{eums_oauth}}", "user_id", "{{eums_user}}", "id", "CASCADE", "CASCADE");
    }
	}

	public function down()
	{
		$this->dropTable("{{eums_oauth}}");
	}

	/*
	// Use safeUp/safeDown to do migration with transaction
	public function safeUp()
	{
	}

	public function safeDown()
	{
	}
	*/
}