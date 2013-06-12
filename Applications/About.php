<?php

class About extends ApplicationBase {
  public function execute( ) {
    //$this->requireAuthentication( );

    $x = XSLTransformer::getInstance( );
    $node = $this->dom->createElement( 'About' );
    $node->appendChild( $this->getLoggedInMemberNode( ) );

    $output = $x->transform( 'About', $node );
    $this->display->appendOutput( $output );
  }
}
