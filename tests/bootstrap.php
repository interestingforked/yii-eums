<?php

// change the following paths if necessary
$yiit=dirname(__FILE__).'/../../../../../yii-1.1.9.r3527/framework/yiit.php';

require_once($yiit);
//require_once(dirname(__FILE__).'/WebTestCase.php');

$config=include(dirname(__FILE__).'/../../../config/test.php');
$config['components']['fixture']['basePath'] = dirname(__FILE__).'/fixtures';

Yii::createWebApplication($config);