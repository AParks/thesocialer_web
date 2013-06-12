<?php

class Register extends ApplicationBase {
  const URL_SUCCESSFUL_LOGIN = '/trending';
  const URL_FAILED_LOGIN = '/login_form';
  
  public function execute( ) {
    // the pass may already have been hashed, but we do it again to add some salt
    $password = $this->hashPassword($_POST['password']);
    $registration = new MemberRegistration( );
    $registration->setDetails( $_POST['firstName'], $_POST['lastName'],
                               $_POST['emailAddress'], $_POST['year'] . '-' . $_POST['month'] . '-' . $_POST['day'],
                               $_POST['gender'], $password );
    
    $registration->setCollege( $_POST['college'] );
    $registration->setLocation( $_POST['location'] );
    $registration->complete( );
    
    try {
      $this->attemptLogin( $_POST['emailAddress'], $_POST['password'] );
      $this->redirect( self::URL_SUCCESSFUL_LOGIN );
    }
    catch ( Exception $e ) {
      $this->redirect( self::URL_FAILED_LOGIN );
    }
  }

  function attemptLogin( $email, $password ) {
    $this->viewer->login( $email, $password );
  }
  
  public function hashPassword($password) {
    $salt = bin2hex(mcrypt_create_iv(32, MCRYPT_DEV_URANDOM)); // get 256 random bits in hex
    $hash = hash("sha256", $salt . $password); // prepend the salt, then hash
    // store the salt and hash in the same string, so only 1 DB column is needed
    $final = $salt . $hash;
    return $final;
  }
}

