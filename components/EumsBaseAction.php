<?php

class EumsBaseAction extends CAction
{

  public $view;

  /**
   * Get EUMS Module
   *
   * @return EumsModule
   */
  public function getModule() {
    return Yii::app()->getModule("eums");
  }

  /**
   *
   *
   * @param string $view
   * @param array $parameters
   * @param bool $return optional, default false
   * @return string
   * @throws CHttpException
   */
  public function render($view, $parameters, $return = false) {
    $json = (strrpos(Yii::app()->getRequest()->getUrl(), '.json') !== false);
    if ($json == true) {
      $parameters = $this->jsonify($parameters);
      /** @var $user CWebUser */
      $user = Yii::app()->user;
      $flashes = $user->getFlashes();
      if (count($flashes) > 0) {
        $parameters['flashes'] = $flashes;
      }
      if ($return) return json_encode($parameters);
      else echo json_encode($parameters);
    } else {
      if (!empty($this->view)) {
        $file = Yii::app()->controller->getViewFile($view);
      } else {
        $file = Yii::app()->controller->getViewFile($view);
      }
      if (empty($file)) {
        $file = Yii::app()->controller->getViewFile('eums.views.actions.'.str_replace('/','.',$view));
        if (!empty($file)) $view = 'eums.views.actions.'.str_replace('/','.',$view);
      }
      if (empty($file)) throw new CHttpException(500, 'View: '.$view.' not found');
      if (Yii::app()->getRequest()->getIsAjaxRequest()) {
        return Yii::app()->controller->renderPartial($view, $parameters, $return);
      } else {
        return Yii::app()->controller->render($view, $parameters, $return);
      }
    }
  }

  /**
   * JSONify Yii object. CModel and CForm.
   *
   * @param array $parameters
   * @return array
   */
  protected function jsonify($parameters) {
    foreach ($parameters as $key=>$param) {
      if (is_array($param)) {
        $parameters[$key] = $this->jsonify($param);
      } else if (is_object($param)) {
        if ($param instanceof CModel) {
          /** @var $record CModel */
          $record = $param;
          $parameters[$key]['attributes'] = $record->getAttributes();
          $parameters[$key]['errors'] = $record->getErrors();
        } else if ($param instanceof CForm) {
          /** @var $form CForm */
          $form = $param;
          $record = $form->getModel();
          $parameters[$key]['errors'] = $record->getErrors();
        } else if (method_exists($param, 'toArray')) {
          $parameters[$key] = $param->toArray();
        }
      }
    }
    return $parameters;
  }

  public function redirect($url) {
    if (Yii::app()->getRequest()->getIsAjaxRequest()) {
      echo json_encode(array('redirect'=>Yii::app()->createAbsoluteUrl($url)));
    } else {
      Yii::app()->controller->redirect($url);
    }
  }
}