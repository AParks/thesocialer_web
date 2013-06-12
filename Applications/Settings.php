<?php

class Settings extends ApplicationBase {
  function execute( ) {
    $this->requireAuthentication( );

    $x = XSLTransformer::getInstance( );
    $node = $this->dom->createElement( 'Settings' );
    $node->appendChild( $this->getLoggedInMemberNode( ) );
    $dob = new DateObject(new DateTime($this->viewer->user->dob));
    $node->appendChild( $dob->toNode( $this->dom ) );
    $node->appendChild( Login::getRegistrationYearsNode( ) );
    $output = $x->transform( 'Settings', $node );    

    $this->assetsManager->addCSS( 'Settings' );
    $this->assetsManager->addJavaScript( 'Settings' );
    $this->display->appendOutput( $output );

  }

}
