<?php

class AdminLocationManager extends AdminBase
{
  public function execute( )
  {
    $x = XSLTransformer::getInstance( );

    $this->assetsManager->addJavaScript( __CLASS__ );
    $this->assetsManager->addCSS( __CLASS__ );

    $node = $this->dom->createElement( __CLASS__ );
    $node->appendChild( $this->getLocationsNode( ) );
    $output = $x->transform( __CLASS__, $node );
    $this->display->appendOutput( $output );
  }

  protected function getLocationsNode( )
  {
    $locationsNode = $this->dom->createElement( 'Locations' );

    $lm = new LocationManager( );

    foreach ( $lm->getLocations( ) as $location )
    {
      $locationsNode->appendChild( $location->toNode( $this->dom ) );
    }

    return $locationsNode;
  }
}
