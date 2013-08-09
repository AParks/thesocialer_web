<?php

class LocationViewer extends ApplicationBase {
  public function execute( ) {
    $x = XSLTransformer::getInstance( );

    $location = $this->getLocation( );
    $date = $this->getDate( );
    $attendanceManager = $this->getAttendanceManager( $location, $date );

    $node = $this->dom->createElement( 'LocationViewer' );
    $node->appendChild( $this->getLoggedInMemberNode( ) );
    $node->appendChild( $this->getLocationNode( $location ) );
    $node->appendChild( $this->getDateNode( new DateObject( $date ) ) );
    $node->appendChild( $this->getAttendeesNode( $attendanceManager ) ); 

    $this->assetsManager->addInitJavaScript( 'var loc = ' . json_encode( $location->getPublicProperties( ) ) . ';' );
    $this->assetsManager->addInitJavaScript( 'var date = "' . $date->format( 'Y-m-d' ) . '";' );
    $this->assetsManager->addInitJavaScript( 'var userStatus = "' . $attendanceManager->getUserStatus( $this->viewer->userId ) . '";' );
    $this->assetsManager->addInitJavaScript("$('.NavigationLink.second').addClass('active');");

    $output = $x->transform( 'LocationViewer', $node );
    $this->display->appendOutput( $output );
    $this->assetsManager->addJavaScript( 'jquery.corner' );
    $this->assetsManager->addJavaScript( 'jquery.linkify-1.0-min' );
    $this->assetsManager->addJavaScript( 'LocationViewer' );
    $this->assetsManager->addJavaScript( 'AttendanceManager' );
    $this->assetsManager->addJavaScript( 'EventSharer' );
    $this->assetsManager->addJavascript( 'https://maps.googleapis.com/maps/api/js?sensor=false', true );
    $this->assetsManager->addCSS( 'LocationViewer' );
    $this->assetsManager->addCSS( 'EventSharer' );
  }

  protected function getLocationNode( Location $location ) {
    return $location->toNode( $this->dom );
  }

  protected function getLocation( ) {
    return new Location( $this->requestValues[0] );
  }

  protected function getDateNode( DateObject $date ) {
    return $date->toNode( $this->dom );
  }

  protected function getDate( ) {
    if ( isset( $this->requestValues[1] ) ) {
      return new DateTime( $this->requestValues[1] );
    }
    return new DateTime( );
  }

  protected function getAttendeesNode( $attendanceManager ) {
    return $attendanceManager->toNode( $this->dom );
  }

  protected function getAttendanceManager( Location $location, DateTime $date ) {
    $am = new AttendanceManager( $location, $date );
    return $am;
  }
}
