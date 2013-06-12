<?php

class Dashboard extends ApplicationBase {
  public function execute( ) {
    $this->requireAuthentication( );

    $x = XSLTransformer::getInstance( );
    $node = $this->dom->createElement( 'Locations' );
    $node->appendChild( $this->getLoggedInMemberNode( ) );

    try {
      $featuredEvent = new FeaturedEvent( );
      //var_export($featuredEvent);
      $node->appendChild( $featuredEvent->toNode( $this->dom ) );
      $eventAM = new FeaturedEventAttendanceManager($featuredEvent);
      $node->appendChild($eventAM->toNode($this->dom));

      //$locJSON = new LocationJSON();
      //$locJSON->getPopularLocations(new DateTime(), 6, 0);
      //$out = $locJSON->getOutput();
      //$this->assetsManager->addInitJavascript( 'var initlocs = '.$out.';');
    }
    catch ( Exception $e ) {
      //var_export($featuredEvent);
    }

    $output = $x->transform( 'NearYou', $node );
    $this->display->appendOutput( $output );
    
    $this->assetsManager->addJavaScript( 'NearYou' );
    $this->assetsManager->addJavaScript( 'AttendanceManager' );

    
    $this->assetsManager->addCSS( 'NearYou' );
    $this->assetsManager->addJavascript( 'https://maps.googleapis.com/maps/api/js?sensor=false', true );
    // DevelopmentFunctions::outputXML( $this->dom, $node );
  }
}
