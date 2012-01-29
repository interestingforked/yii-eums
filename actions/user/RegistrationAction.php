<?php
class RegistrationAction extends EumsBaseAction
{
  public $registrationCompleteUrl = "/";
  public $loginUrl = array('login');

  public function run() {
    $action = isset($_GET['action'])?$_GET['action']:'index';
    switch ($action) {
      case 'activationemail':
        $this->actionSendActivationEmail();
        break;
      case 'confirm':
        $this->actionConfirm();
        break;
      default:
        $this->actionIndex();
    }
  }

  /**
   * @todo
   */
  protected function actionSendActivationEmail() {
  }

  protected function actionConfirm() {
    $user = new EumsUser("accountactivation");
    $form = $this->getForm('user.accountactivation', $user);
    if ($form->submitted() && $form->validate()) {
      $user = EumsUser::model()->findByAttributes(array(
        'activation'=>$user->activation,
        'username'=>$user->username,
      ));
      if ($user != null) {
        $user->activation = null;
        $user->active = 1;
        if ($user->save()) {
          $this->onRegisterConfirm(new CEvent($this, array('user'=>$user)));
          /** @var $user CWebUser */
          $user = Yii::app()->user;
          $user->setFlash("info", "Account activated. Please login");
          $this->redirect($this->loginUrl);
        }
      }
      /** @var $user CWebUser */
      $user = Yii::app()->user;
      $user->setFlash("error", "Could not activate your account. Please try again later.");
    }
    $this->render("user.registration", array('form'=>$form));
  }

  protected function actionIndex() {
    $user = new EumsUser("registration");
    $form = $this->getForm('user.register', $user);
    if ($form->submitted() && $form->validate()) {
      $model = $form->getModel();
      if ($model->save()  && $this->sendActivationEmail($model)) {
        $this->onRegistered(new CEvent($this, array('user'=>$model)));
        /** @var $user CWebUser */
        $user = Yii::app()->user;
        $user->setFlash("info", "Registration successful. Please check your mailbox for activation email");
        $this->redirect($this->registrationCompleteUrl);
      } else {
        /** @var $user CWebUser */
        $user = Yii::app()->user;
        $user->setFlash("error", "System error. Registration unsuccessful.");
      }
    }
    $this->render('user.registration', array('form'=>$form));
  }

  /**
   * On Registration confirmed.
   *
   * @param CEvent $event with CModel user
   */
  public function onRegisterConfirm($event) {
    $this->raiseEvent('onRegisterConfirm', $event);
  }

  /**
   * On Registration Success.
   * @param CEvent $event with CModel user
   */
  public function onRegistered($event) {
    $this->raiseEvent('onRegistered', $event);
  }

  /**
   * Method to send activation email
   *
   * @todo
   * @param EumsUser $user
   */
  protected function sendActivationEmail($user) {
    return true;
  }
}
