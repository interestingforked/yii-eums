<?php

class PasswordLoginAction extends EumsBaseAction
{
  public function run() {
    $user = new EumsUser("login");
    $form = $this->getModule()->buildForm('user.login', $user);
    if ($form->submitted() && $form->validate()) {
      $model = $form->getModel();
      $identity=new PasswordBasedUserIdentity($model->username,$model->password);
      if($identity->authenticate()) {
        Yii::app()->user->login($identity);
        /** @var $user CWebUser */
        $user = Yii::app()->user;
        $this->redirect($user->getState("home"));
      } else {
        $model->addError("password", $this->getIdentityErrorMessage($identity->errorCode));
      }
    }
    $this->render('user.login', array('form'=>$form));
  }

  protected function getIdentityErrorMessage($errorCode) {
    switch ($errorCode) {
      case PasswordBasedUserIdentity::ERROR_ACCOUNT_BLOCKED:
        return "Your account is blocked, please contact administrator if you have any question.";
      case PasswordBasedUserIdentity::ERROR_ACCOUNT_NEED_ACTIVATION:
        return "Your account is not activate yet. Check your email box for an activation email.";
      case PasswordBasedUserIdentity::ERROR_PASSWORD_INVALID:
        return "Incorrect Password";
      case PasswordBasedUserIdentity::ERROR_USERNAME_INVALID:
        return "Incorrect Password";
      default:
        return 'System Error';
    }
  }
}
