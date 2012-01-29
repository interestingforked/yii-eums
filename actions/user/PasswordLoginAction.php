<?php

class PasswordLoginAction extends EumsBaseAction
{
  public $defaultHome = "/";

  public function run() {
    $user = new EumsUser("login");
    $form = $this->getForm('user.login', $user);
    if ($form->submitted() && $form->validate()) {
      $model = $form->getModel();
      $identity=new PasswordBasedUserIdentity($model->username,$model->password);
      if($identity->authenticate()) {
        Yii::app()->user->login($identity);
        $eumsUser = EumsUser::model()->findByAttributes(array('username'=>$model->username));
        $eumsUser->last_visited = date('Y-m-d H:i:s');
        $eumsUser->save();
        $this->redirect(Yii::app()->user->getState("home", $this->defaultHome));
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
