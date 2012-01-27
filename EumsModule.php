<?php

class EumsModule extends CWebModule
{
	public function init()
	{
		// this method is called when the module is being created
		// you may place code here to customize the module or the application

		// import the module-level models and components
		$this->setImport(array(
			'eums.models.*',
			'eums.components.*',
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
      $file = Yii::app()->controller->getViewFile('/forms'.$formConfiguration);
      $formConfiguration = include($file);
    }
    if (isset($this->formBuilder) && is_array($this->formBuilder)) return Yii::createComponent($this->formBuilder, $formConfiguration, $model);
    else return new CForm($formConfiguration, $model);
  }
}
