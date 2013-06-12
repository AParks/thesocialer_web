<?php

class Network extends ApplicationBase
{
  function execute( )
  {
    $this->requireAuthentication( );

    $x = XSLTransformer::getInstance( );
    $node = $this->dom->createElement( 'Network' );
    $node->appendChild( DOMGenerator::getNode( $this->dom, $this->_getFriends( ), 'Friends' ) );
    $output = $x->transform( 'Network', $node );
    $this->assetsManager->addCSS( 'Network' );
    $this->assetsManager->addCSS( 'FriendRequestManager' );
    $this->assetsManager->addJavaScript( 'FriendNetwork' );
    $this->assetsManager->addJavaScript( 'FriendRequestManager' );
    $this->assetsManager->addInitJavaScript("$('.NavigationLink.fourth').addClass('active')");

    $this->display->appendOutput( $output );

    // DevelopmentFunctions::outputXML( $this->dom, $node );
  }

  private function _getFriends( )
  {
    $friends = array( );

    $friendsObject = new Friends( );
    foreach ( $friendsObject->get( $this->viewer->user ) as $friendId )
    {
      $friends[] = new Member( $friendId );
    }
    return $friends;
  }
}
