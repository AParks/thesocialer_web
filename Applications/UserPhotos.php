<?php

class UserPhotos extends ApplicationBase
{
  function execute( )
  {
    $this->requireAuthentication( );

    $user = new Member( $this->requestValues[0] );

    $x = XSLTransformer::getInstance( );
    $node = $this->dom->createElement( 'UserPhotos' );
    $node->appendChild( $user->toNode( $this->dom ) );
    $node->appendChild( $this->getLoggedInMemberNode( $this->dom ) ); 
    $node->appendChild( $this->_getUserPhotosNode( $user ) );
    $output = $x->transform( 'UserPhotos', $node );
    $this->display->appendOutput( $output );

    $this->assetsManager->addJavaScript( 'UserPhotos' );
    $this->assetsManager->addCSS( 'UserPhotos' );
    // DevelopmentFunctions::outputXML( $this->dom, $node );
  }

  private function _getUserPhotosNode( Member $user )
  {
    $photosNode = $this->dom->createElement( 'Photos' );

    $photoManager = new PhotoManager( );
    foreach ( $photoManager->getUserPhotos( $user->userId ) as $photo )
    {
      $photosNode->appendChild( $photo->toNode( $this->dom ) );
    }

    return $photosNode;
  }
}
