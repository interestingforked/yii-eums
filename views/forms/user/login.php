<?php
return array(
  'attributes'=>array(
    'id'=>'user-login-form'
  ),
  'elements'=>array(
    '<p class="note">'.Yii::t('userGroupsModule.general', 'Fields with {*} are required.', array('{*}' => '<span class="required">*</span>')).'</p>',
    'username'=>array(
      'type'=>'text',
    ),
    'password'=>array(
      'type'=>'password',
    ),
  ),
  'buttons'=>array(
    'submit'=>array(
      'type'=>'submit',
      'label'=>'Sign In',
      'class'=>'primary',
    ),
  ),
);