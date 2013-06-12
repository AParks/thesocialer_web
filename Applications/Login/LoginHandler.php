<?php

class LoginHandler extends JSONApplicationBase {
 // const URL_SUCCESSFUL_LOGIN = '/trending';
 // const URL_FAILED_LOGIN = '/login_form';
 // const URL_FAILED_LOGIN = '/?loginFailed=1';

  function execute( ) {
    try {
      $this->attemptLogin( $_POST['LoginEmail'], $_POST['LoginPassword'], $_POST['RememberMe'] );
    }
    catch ( Exception $e ) {
      error_log($e->getFile().$e->getLine().$e->getMessage());
      die('Invalid Login');
    }
      exit;
  }

  function attemptLogin( $email, $password, $remmber ) {
    $this->viewer->login( $email, $password, $remmber );
  }
}
