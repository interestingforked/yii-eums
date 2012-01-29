<?php
return array(
  'attributes'=>array(
    'id'=>'user-passwordreset-form'
  ),
  'elements'=>array(
    'email'=>array(
      'type'=>'text',
    ),
    'password'=>array(
      'type'=>'password',
    ),
    'password_confirm'=>array(
      'type'=>'password',
    ),
  ),
  'buttons'=>array(
    'submit'=>array(
      'type'=>'submit',
      'label'=>'Password Reset',
      'class'=>'primary',
    ),
  ),
);