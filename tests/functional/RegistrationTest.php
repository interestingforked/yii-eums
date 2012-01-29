<?php

class RegistrationTest extends CWebDriverTestCase {

  public $fixtures=array(
    'eumsUsers'=>'EumsUser',
  );

  public function testRegisterActivationSignIn() {
    $this->open('/eums/user/register');
    $this->type("EumsUser_username", 'tester0');
    $this->type("EumsUser_first_name", 'Tester 0');
    $this->type("EumsUser_last_name", 'Tester 0');
    $this->type("EumsUser_email", 'tester0@test.com');
    $this->type("EumsUser_password", 'abc');
    $this->type("EumsUser_password_confirm", 'abc');
    $button = $this->getElement(LocatorStrategy::cssSelector, '#user-register-form .actions .primary');
    $button->click();
    $this->assertTrue($this->isTextPresent("Registration successful"));
    /** @var $user EumsUser */
    $user = EumsUser::model()->findByAttributes(array('username'=>'tester0'));
    $this->assertNotNull($user);
    $this->open('/eums/user/register/action/confirm');
    $this->type("EumsUser_username", 'tester0');
    $this->type("EumsUser_activation", $user->activation);
    $button = $this->getElement(LocatorStrategy::cssSelector, '#user-activation-form .actions .primary');
    $button->click();
    $this->assertTrue($this->isTextPresent("Account activated"));
    /** @var $user EumsUser */
    $user = EumsUser::model()->findByAttributes(array('username'=>'tester0'));
    $this->assertEquals("1", $user->active);
  }
}