<?php

class ResetPassword extends ApplicationBase {
  public function execute( ) {
    if ( $this->viewer->isAuthenticated( ) === true ) {
      $this->redirect('/explore');
    }

    $x = XSLTransformer::getInstance( );
    if ( isset( $_GET['c'] ) && $this->validCode( $_GET['c'] ) ) {
      $node = $this->dom->createElement( 'ResetPassword' );
      $output = $x->transform( 'ResetPassword', $node );
      $this->assetsManager->addJavaScript( 'ResetPassword' );
      
      $this->assetsManager->addInitJavaScript( 'var resetcode = "'.$_GET['c'].'";' );
    }
    else {
      $node = $this->dom->createElement( 'ResetPasswordInvalid' );
      $output = $x->transform( 'ResetPassword', $node );
    }
    $this->assetsManager->addCSS( 'ResetPassword' );
    $this->display->appendOutput( $output );
  }

  protected function validCode( $code ) {
    $pdo = sPDO::getInstance( );
    $query = $pdo->prepare( 'SELECT reset_password_valid_code( :code )' );
    $query->bindParam( ':code', $code );
    return $query->execute( );
  }
}
