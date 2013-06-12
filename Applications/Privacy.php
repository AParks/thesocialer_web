<?php

class Privacy extends ApplicationBase {
  public function execute( ) {
  //  $this->requireAuthentication( );

    $x = XSLTransformer::getInstance( );
    $node = $this->dom->createElement( 'Privacy' );
    $node->appendChild( $this->getLoggedInMemberNode( ) );

    $output = $x->transform( 'Privacy', $node );
    $this->display->appendOutput( $output );
  }
}
