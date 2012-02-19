<?php
class LogoutAction extends CAction {
  public function run() {
    Yii::app()->user->logout();
    Yii::app()->user->setFlash('sucess', 'Logout Successfully');
    Yii::app()->controller->redirect(array('login'));
  }
}
