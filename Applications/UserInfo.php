<?php

class UserInfo extends JSONApplicationBase {
  function execute( ) {
    $userId = (int) $this->requestValues[0];
    $user = new Member($userId);

    $response = array( );
    $fields = explode(',', $_GET['fields'] );
    foreach ( $fields as $field ) {
      try {
        $response[$field] = $user->{$field};
      }
      catch ( Exception $e ){}
    }

    $this->addOutput( $response );
    $this->output( );
    exit;
  }
}
