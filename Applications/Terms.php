<?php

class Terms extends ApplicationBase {
  public function execute( ) {

    $x = XSLTransformer::getInstance( );
    $node = $this->dom->createElement( 'Terms' );
    $node->appendChild( $this->getLoggedInMemberNode( ) );
        $this->assetsManager->addInitJavaScript("$('.RightFoot a.second').addClass('active');");

    $output = $x->transform( 'Terms', $node );
    $this->display->appendOutput( $output );
  }
}
