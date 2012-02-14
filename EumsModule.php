<?php

class EumsModule extends CWebModule
{
  public $formBuilder;
  public $oauth;

  protected $oauthProvider = array(
    'twitter'=>array(
      'scope'=>'https://api.twitter.com',
      'provider'=>array(
        'request'=>'https://api.twitter.com/oauth/request_token',
        'authorize'=>'https://api.twitter.com/oauth/authorize',
        'access'=>'https://api.twitter.com/oauth/access_token',
      ),
    ),
  );

	public function init()
	{
		// import the module-level models and components
		$this->setImport(array(
			'eums.models.*',
			'eums.components.*',
      'eums.actions.user.*',
      'eums.extensions.eoauth.*',
      'eums.extensions.eoauth.lib.*',
		));
	}

	public function beforeControllerAction($controller, $action)
	{
		if(parent::beforeControllerAction($controller, $action))
		{
			// this method is called before any module controller action is performed
			// you may place customized code here
			return true;
		}
		else
			return false;
	}

  public function buildForm($formConfiguration, $model) {
    if (is_string($formConfiguration)) {
      $file = Yii::app()->controller->getViewFile('/forms/'.str_replace('.','/',$formConfiguration));
      if (empty($file)) $file = Yii::app()->controller->getViewFile('eums.views.forms.'.str_replace('/','.',$formConfiguration));
      if (empty($file)) throw new CHttpException(500, 'Form view: '.$formConfiguration.' not found');
      $formConfiguration = include($file);
    }
    if (isset($this->formBuilder) && is_array($this->formBuilder)) return Yii::createComponent($this->formBuilder, $formConfiguration, $model);
    else return new CForm($formConfiguration, $model);
  }

  /**
   * Get OAuth User Identity
   *
   * @param string $type e.g twitter, facebook, google
   * @return EOAuthUserIdentity|null
   */
  public function getOAuthUserIdentity($type) {
    $identity = null;
    if (isset($this->oauth[$type])) {
      if (isset($this->oauthProvider[$type])) {
        $identity = new EOAuthUserIdentity(CMap::mergeArray($this->oauth[$type], $this->oauthProvider[$type]));
      } else {
        $identity = new EOAuthUserIdentity(CMap::mergeArray($this->oauth[$type], $this->oauthProvider[$type]));
      }
    }
    return $identity;
  }
}
