<?php

class Terms extends ApplicationBase {
  public function execute( ) {
 //   $this->requireAuthentication( );

    $x = XSLTransformer::getInstance( );
    $node = $this->dom->createElement( 'Terms' );
    $node->appendChild( $this->getLoggedInMemberNode( ) );

    $output = $x->transform( 'Terms', $node );
    $this->display->appendOutput( $output );
  }
}
