<?php

define('TEST_BASE_URL','http://mobilepos.devel.int/index-test.php');

// change the following paths if necessary
$yiit=dirname(__FILE__).'/../../../../../yii-1.1.9.r3527/framework/yiit.php';

require_once($yiit);

$config=include(dirname(__FILE__).'/../../../config/test.php');
$config['components']['fixture']['basePath'] = dirname(__FILE__).'/fixtures';

$app = Yii::createWebApplication($config);
$app->getModule("eums");

Yii::import('eums.tests.webdriver-bindings.*');

require_once(dirname(__FILE__).'/WebTestCase.php');