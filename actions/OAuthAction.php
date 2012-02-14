<?php
/**
 * oAuth authentication action.
 */
class OAuthAction extends CAction {

  public $type;
  public $registerUrl = "register";

  public function run() {
    /** @var $user CWebUser */
    $user=Yii::app()->user;
    /** @var $module EumsModule */
    $module = Yii::app()->getModule("eums");
    $identity = $module->getOAuthUserIdentity($this->type);
    if ($identity->authenticate()) {
      if ($user->getIsGuest()) {
        if (!$this->loginUser($identity)) {
          /** Forward to Registration Page */
        }
      } else {
        $this->assignOauth2User($identity);
      }
      if (isset($this->forwardUrl))
        Yii::app()->controller->forward($this->forwardUrl);
      else
        Yii::app()->controller->redirect($user->returnUrl);
    } else {
      if ($identity->getError()) {
        $user->setFlash('error', $identity->getError());
      }
    }
  }

  /**
   * Login user with OAuth User Identity
   *
   * @param $identity EOAuthUserIdentity
   * @param bool Login success? or Registration needed?
   */
  protected function loginUser($identity) {
    /** @var $user CWebUser */
    $user = Yii::app()->user;
    /** @var $oauth EumsOauth */
    $oauth = EumsOauth::model()->find("`key` = :key and `active` = 1", array(':key'=>$identity->getId()));
    if ($oauth != null) {
      $user->login($identity);
      $user->setId($oauth->eums_user->getPrimaryKey());
      $user->setName($oauth->eums_user->username);
      return true;
    }
    return false;
  }

  /**
   * Assign OAuth to User
   *
   * @param EOAuthUserIdentity $identity
   * @return EumsOauth
   */
  protected function assignOauth2User($identity) {
    /** @var $user EumsUser */
    $user = EumsUser::model()->findByPk(Yii::app()->user->getId());
    $oauths = $user->eums_oauth(array('condition'=>'`key` = :key', 'params'=>array(':key'=>$identity->getId())));
    if (count($oauths) == 0) {
      $oauth = new EumsOauth();
      $oauth->provider = $this->type;
      $oauth->user_id = $user->getPrimaryKey();
      $oauth->key = $identity->getProvider()->token->key;
      $oauth->secret = $identity->getProvider()->token->secret;
      $oauth->date_created = date('Y-m-d H:i:s');
      $oauth->save();
    } else {
      $oauth = $oauths[0];
    }
    return $oauth;
  }
}