<?php

class Profile extends ApplicationBase {
  protected $userId;

  function execute( ) {
    $this->requireAuthentication( );
    
    if( $this->requestValues )
      $this->userId = (int) $this->requestValues[0];
    
    if ( Member::validUserId( $this->userId ) === false ) {
      $this->redirect( '/profile/' . $this->viewer->user->userId );
    }
    
    $x = XSLTransformer::getInstance( );
    $node = $this->dom->createElement( 'Profile' );
    
    $user = new Member( $this->userId );
    $node->appendChild( $user->toNode( $this->dom ) );
    $node->appendChild( $this->getLoggedInMemberNode( ) );
    $node->appendChild($this->getUserPhotosNode(7));
    
    /*$quickPicks = $user->getQuickPicks( );
    $node->appendChild( DOMGenerator::getNode( $this->dom, $quickPicks, 'QuickPicks' ) );*/
    
    $node->appendChild( $this->getFriendsNode( $user, 10 ) );
    
    $output = $x->transform( 'Profile', $node );
    
    $this->display->appendOutput( $output );
    
    $this->assetsManager->addJavaScript( 'Profile' );
    $this->assetsManager->addJavaScript( 'jquery.Jcrop.min' );
    $this->assetsManager->addJavaScript( 'jquery.color' );
    $this->assetsManager->addJavaScript( 'AttendanceManager' );
    $this->assetsManager->addCSS( 'Profile' );
    $this->assetsManager->addCSS( 'jquery.Jcrop.min' );
    $this->assetsManager->addInitJavaScript( '$(function( ){var profile = new Profile( ' . json_encode( $this->getUserDetails( $user ) ) . ' );});' );
    $this->assetsManager->addInitJavaScript("$('.NavigationLink.fifth').addClass('active')");

    $this->assetsManager->addJavaScript( 'PhotoUploader' );
    $this->assetsManager->addCSS( 'PhotoUploader' );

    $this->assetsManager->addJavaScript( 'MessageSender' );
    $this->assetsManager->addCSS( 'MessageSender' );

    $this->assetsManager->addJavaScript( 'FriendRequestManager' );
    $this->assetsManager->addCSS( 'FriendRequestManager' );
    // DevelopmentFunctions::outputXML( $this->dom, $node );
  }

  protected function getUserDetails( $user ) {
    $details = array( );
    $details['userId'] = $user->userId;
    $details['photo'] = $user->photo->paths[Photo::SIZE_SMALL];
    $details['firstName'] = $user->firstName;
    $details['lastName'] = $user->lastName;
    $details['gender'] = $user->gender;
    return $details;
  }

  protected function getUserPhotosNode($limit) {
    $photosNode = $this->dom->createElement( 'photos' );

    $photoManager = new PhotoManager( );
    $count = 0;
    $hasMore = false;

    foreach ( $photoManager->getUserPhotos( $this->userId ) as $photo ) {
      if ( $count >= $limit ) {
        $hasMore = true;
        break;
      }

      /*if ( $photo->isDefault ) {
        continue;
      }*/
      $photosNode->appendChild($photo->toNode($this->dom));
      $count++;
    }

    $photosNode->setAttribute('hasMore', $hasMore);

    return $photosNode;
  }

  protected function getFriendsNode(Member $user, $limit) {
    $friendsNode = $this->dom->createElement('Friends');
    $friends = new Friends( );

    foreach ( array_slice( $friends->get( $user ), 0, $limit ) as $friend ) {
      $friendsNode->appendChild( $this->dom->createElement( 'friend', $friend ) );
    }

    $friendsNode->setAttribute( 'friendsWithViewer', $friends->areFriends(Viewer::getInstance( )->user, $user));

    return $friendsNode;
  }
}
