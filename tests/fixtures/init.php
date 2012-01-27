<?php
Yii::setPathOfAlias('eums', dirname(__FILE__).'/../../');

Yii::import('eums.migrations.*');
$files = CFileHelper::findFiles(
  dirname(__FILE__).'/../../migrations',
  array(
    'fileTypes'=>array('php'),
    'level'=>0,
  )
);
ob_start();
foreach ($files as $file) {
  $file = substr($file, 0, -4);
  $file = substr($file, strrpos($file, '/')+1);
  /** @var $migration CDbMigration */
  $migration = new $file();
  $migration->down();
}
foreach ($files as $file) {
  $file = substr($file, 0, -4);
  $file = substr($file, strrpos($file, '/')+1);
  /** @var $migration CDbMigration */
  $migration = new $file();
  $migration->up();
}
ob_clean();