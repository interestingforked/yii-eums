<?php

class UserController extends Controller
{

  public function actions() {
    return array(
      'login'=>'eums.actions.user.PasswordLoginAction',
      'register'=>'eums.actions.user.RegistrationAction',
      'forgot'=>'eums.actions.user.ForgotPasswordAction',
      'captcha'=>'CCaptchaAction',
    );
  }
}
