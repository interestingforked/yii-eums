<?php

class ForgotPasswordAction extends EumsBaseAction
{
  public $loginUrl = array('login');

  public function run() {
    $action = isset($_GET['action'])?$_GET['action']:'index';
    switch ($action) {
      case 'confirm':
        $this->actionConfirm();
        break;
      default:
        $this->actionIndex();
    }
  }

  protected function actionConfirm() {
    /** @var $user CWebUser */
    $user = Yii::app()->user;
    if (!isset($_GET['token'])) {
      throw new CHttpException(500, "Missing token");
    } else {
      /** @var $eumsuser EumsUser */
      $eumsuser = EumsUser::model()->findByAttributes(array('active'=>1, 'activation'=>$_GET['token']));
      if ($eumsuser == null) {
        $user->setFlash("error", "Invalid token");
        $form = $this->getForm('user.reset-forgotpassword', new EumsUser());
      } else {
        $eumsuser->setScenario("resetforgotpassword");
        unset($eumsuser->password);
        $form = $this->getForm('user.reset-forgotpassword', $eumsuser);
        if ($form->submitted() && $form->validate()) {
          $eumsuser->activation = '';
          if ($eumsuser->save()) {
            $user->setFlash('info', 'Password reset successfully. Please login with the new password');
            $this->redirect($this->loginUrl);
          } else {
            $user->setFlash("error", "System error. Please try again later.");
          }
        }
      }
    }
    $this->render('user.forgotpassword', array('form'=>$form));
  }

  protected function actionIndex() {
    /** @var $user CWebUser */
    $user = Yii::app()->user;
    $eumsuser = new EumsUser("forgotpassword");
    $form = $this->getForm('user.forgotpassword', $eumsuser);
    if ($form->submitted() && $form->validate()) {
      $model = $form->getModel();
      $eumsuser = EumsUser::model()->findByAttributes(array('email'=>$model->email));
      if ($eumsuser != null) {
        if (!$eumsuser->active) {
          $user->setFlash("error", "Account is not activate yet");
        } else {
          $eumsuser->setScenario("forgotpassword");
          if ($eumsuser->save() && $this->sendPasswordResetEmail($eumsuser)) {
            $user->setFlash("info", "Reset password link has been sent to your email address");
          } else {
            $user->setFlash("error", "System error. Please try again later.");
          }
        }
      } else {
        $user->setFlash("error", "Email address is not register in the System");
        $model->addError("email", "Not such email");
      }
    }
    $this->render('user.forgotpassword', array('form'=>$form));
  }

  /**
   * @todo
   * @param EumsUser $user
   * @return bool
   */
  protected function sendPasswordResetEmail($user) {
    return true;
  }
}
