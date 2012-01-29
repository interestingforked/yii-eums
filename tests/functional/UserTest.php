<?php

class RegistrationTest extends CWebDriverTestCase {

  public $fixtures=array(
    'eumsUsers'=>'EumsUser',
  );

  public function testSignIn() {
    $this->open('/eums/user/login');
    $this->type("EumsUser_username", 'tester1');
    $this->type("EumsUser_password", 'asd');
    $button = $this->getElement(LocatorStrategy::cssSelector, '#user-login-form .actions .primary');
    $button->click();
    sleep(1);
    $user = EumsUser::model()->findByAttributes(array('username'=>'tester1'));
    $this->assertNotNull($user);
    $this->assertNotEmpty($user->last_visited);
  }

  public function testRegisterActivation() {
    $this->open('/eums/user/register');
    $this->type("EumsUser_username", 'tester0');
    $this->type("EumsUser_first_name", 'Tester 0');
    $this->type("EumsUser_last_name", 'Tester 0');
    $this->type("EumsUser_email", 'tester0@test.com');
    $this->type("EumsUser_password", 'abc');
    $this->type("EumsUser_password_confirm", 'abc');
    $button = $this->getElement(LocatorStrategy::cssSelector, '#user-register-form .actions .primary');
    $button->click();
    sleep(1);
    $this->assertTrue($this->isTextPresent("Registration successful"));
    /** @var $user EumsUser */
    $user = EumsUser::model()->findByAttributes(array('username'=>'tester0'));
    $this->assertNotNull($user);
    $this->open('/eums/user/register/action/confirm');
    $this->type("EumsUser_username", 'tester0');
    $this->type("EumsUser_activation", $user->activation);
    $button = $this->getElement(LocatorStrategy::cssSelector, '#user-activation-form .actions .primary');
    $button->click();
    sleep(1);
    $this->assertTrue($this->isTextPresent("Account activated"));
    /** @var $user EumsUser */
    $user = EumsUser::model()->findByAttributes(array('username'=>'tester0'));
    $this->assertEquals("1", $user->active);
  }


  public function testForgotPassword() {
    $this->open('/eums/user/forgot');
    $this->type("EumsUser_email", 'tester1@test.com');
    $button = $this->getElement(LocatorStrategy::cssSelector, '#user-forgotpassword-form .actions .primary');
    $button->click();
    sleep(1);
    /** @var $user EumsUser */
    $user = EumsUser::model()->findByAttributes(array('email'=>'tester1@test.com'));
    $this->assertNotNull($user);
    $originalHashPassword = $user->password;
    $this->assertNotEmpty($user->activation);
    $this->open('/eums/user/forgot/action/confirm/token/'.$user->activation);
    $this->type("EumsUser_password", 'xxyyzz');
    $this->type("EumsUser_password_confirm", 'xxyyzz');
    $button = $this->getElement(LocatorStrategy::cssSelector, '#user-resetforgotpassword-form .actions .primary');
    $button->click();
    sleep(1);
    $user = EumsUser::model()->findByAttributes(array('email'=>'tester1@test.com'));
    $this->assertNotNull($user);
    $this->assertNotEquals($originalHashPassword, $user->password);
    $this->assertEmpty($user->activation);
  }
}