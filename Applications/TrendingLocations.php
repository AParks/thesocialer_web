<?php

class TrendingLocations extends ApplicationBase {
  public function execute( ) {
    $this->requireAuthentication( );

    $x = XSLTransformer::getInstance( );
    $node = $this->dom->createElement( 'Home' );
    $node->appendChild( $this->getDaysNode( ) );
    $node->appendChild( $this->getLoggedInMemberNode( ) );
    $output = $x->transform( 'Dashboard', $node );
    $this->display->appendOutput( $output );

    $this->assetsManager->addJavaScript( 'jquery.corner' );
    $this->assetsManager->addJavaScript( 'Dashboard' );
    $this->assetsManager->addJavaScript( 'jquery.linkify-1.0-min' );
    $this->assetsManager->addJavaScript( 'AttendanceManager' );
    $this->assetsManager->addJavaScript( 'FriendRequestManager' );
    $this->assetsManager->addCSS( 'FriendRequestManager' );
    $this->assetsManager->addCSS( 'Dashboard' );
    //DevelopmentFunctions::outputXML( $this->dom, $node );
  }

  protected function getDaysNode( ) {
    $node = $this->dom->createElement( 'EventDays' );
    $date = new DateTime( );

    for ( $i = 0; $i < 4; $i++ ) {
      $dayNode = $this->dom->createElement( 'Day', $date->format( 'Y-n-j' ) );

      if ( $i === 0 ) {
        $displayText = 'Today';
      }
      elseif ( $i === 1 ) {
        $displayText = 'Tomorrow';
      }
      else {
        $displayText = $date->format( 'l' ); 
      }

      $dayNode->setAttribute( 'text', $displayText );
      $dayNode->setAttribute( 'index', $i );
      $node->appendChild( $dayNode );
      $date->modify( '+1 day' );
    }

    return $node;
  }
}
