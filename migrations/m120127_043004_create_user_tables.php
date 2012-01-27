<?php

class m120127_043004_create_user_tables extends CDbMigration
{
	public function up()
	{
    $this->createTable("{{eums_user}}", array(
      'id'=>'pk',
      'username'=>'string not null',
      'first_name'=>'string not null',
      'last_name'=>'string not null',
      'email'=>'string not null',
      'password'=>'string not null',
      'salt'=>'string not null',
      'activation'=>'string',
      'active'=>'boolean default 0',
      'home'=>'string',
      'date_created'=>'datetime',
      'last_visited'=>'datetime',
    ));
    $this->createIndex("eums_user_username_uidx", "{{eums_user}}", "username", true);
    if (strpos($this->getDbConnection()->getDriverName(), "mysql") !== false) {
      $this->getDbConnection()->createCommand("ALTER TABLE {{eums_user}} ENGINE = INNODB")->execute();
    }
	}

	public function down()
	{
    $this->dropTable("{{eums_user}}");
	}
}