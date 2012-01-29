<?php

class PasswordBasedUserIdentity extends CUserIdentity
{
  protected $_id;

  const ERROR_ACCOUNT_BLOCKED = 3;
  const ERROR_ACCOUNT_NEED_ACTIVATION = 4;

  public function authenticate() {
    /** @var $user EumsUser */
    $user = EumsUser::model()->findByAttributes(array('username'=>$this->username));
    if ($user !== null) {
      if ($user->authenticate($this->password)) {
        $this->_id = $user->getPrimaryKey();
        $this->errorCode = self::ERROR_NONE;
        $this->setState("username", $user->username);
        $this->setState("home", $user->home);
      } else {
        if ($user->active) {
          $this->errorCode = self::ERROR_PASSWORD_INVALID;
        } else {
          if (!empty($user->activation)) {
            $this->errorCode = self::ERROR_ACCOUNT_NEED_ACTIVATION;
          } else {
            $this->errorCode = self::ERROR_ACCOUNT_BLOCKED;
          }
        }
      }
    } else {
      $this->errorCode = self::ERROR_USERNAME_INVALID;
    }
    return !$this->errorCode;
  }

  public function getId() {
    return $this->_id;
  }
}
