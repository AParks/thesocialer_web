<?php

class ProfileJSON extends JSONApplicationBase {
  const ACTION_SAVE = 'save';
  const ACTION_PASS = 'pass';

  public function execute( ) {
    $success = false;
    //error_log('success: '.$success);
    switch ( $this->requestValues[0] ) {
    case self::ACTION_SAVE:
      // FirstName=Matt&LastName=Kemmerer&College=&Location=&AboutMe=
      $success = $this->saveProfile( $this->viewer->user, $_POST );
      break;
    case self::ACTION_PASS:
      // FirstName=Matt&LastName=Kemmerer&College=&Location=&AboutMe=
      $success = $this->changePassword( $this->viewer->user, $_POST );
      break;
    }
    //error_log('success: '.$success);
    $this->addOutput( 'result', $success );
    $this->output( );
    exit;
  }

  protected function saveProfile( Member $user, $values ) {
    return $user->update( $values );
  }
  protected function changePassword( Member $user, $values ) {
    return $user->updatePassword( $values );
  }
}
