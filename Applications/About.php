<?php

class About extends ApplicationBase {
  public function execute( ) {
    //$this->requireAuthentication( );

    $x = XSLTransformer::getInstance( );
    $node = $this->dom->createElement( 'About' );
    $node->appendChild( $this->getLoggedInMemberNode( ) );
        $this->assetsManager->addInitJavaScript("$('.RightFoot a.last').addClass('active');");

    $output = $x->transform( 'About', $node );
    $this->display->appendOutput( $output );
  }
}
