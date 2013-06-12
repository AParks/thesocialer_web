<?php

class Explore extends ApplicationBase {
  public function execute( ) {
 #   $this->requireAuthentication( );

    $x = XSLTransformer::getInstance( );
    $node = $this->dom->createElement( 'Locations' );
    $node->appendChild( $this->getLoggedInMemberNode( ) );

    try {
      $featuredEvent = new FeaturedEvent( );
      $node->appendChild( $featuredEvent->toNode( $this->dom ) );
      $eventAM = new FeaturedEventAttendanceManager($featuredEvent);
      $node->appendChild($eventAM->toNode($this->dom));

    }
    catch ( Exception $e ) {
      //var_export($featuredEvent);
    }

    $output = $x->transform( 'Explore', $node );
    $this->display->appendOutput( $output );
    
    $this->assetsManager->addJavaScript( 'Explore' );
    $this->assetsManager->addJavaScript( 'AttendanceManager' );

    
    $this->assetsManager->addCSS( 'Explore' );
    $this->assetsManager->addJavascript( 'https://maps.googleapis.com/maps/api/js?sensor=false', true );
    $this->assetsManager->addInitJavaScript("$('.NavigationLink.second').addClass('active')");


  }
}
