<?php

class Search extends ApplicationBase {
  public function execute( ) {
    $this->requireAuthentication( );

    $x = XSLTransformer::getInstance( );
    $node = $this->dom->createElement( 'Search' );
    $node->appendChild( $this->getLoggedInMemberNode( ) );

    $output = $x->transform( 'Search', $node );
    $this->display->appendOutput( $output );

    $this->assetsManager->addJavaScript( 'Search' );
    $this->assetsManager->addJavaScript( 'FriendRequestManager' );
    $this->assetsManager->addInitJavaScript("$('.NavigationLink.sixth').addClass('active')");

    $this->assetsManager->addCSS( 'FriendRequestManager' );
    $this->assetsManager->addCSS( 'Search' );
    $this->assetsManager->addCSS( 'UserRecommendations' );
    // DevelopmentFunctions::outputXML( $this->dom, $node );
  }
}
