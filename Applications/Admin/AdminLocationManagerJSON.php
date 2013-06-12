<?php

class AdminLocationManagerJSON extends JSONApplicationBase
{
  const ACTION_ADD = 'add';
  const ACTION_DELETE = 'delete';

  public function execute( )
  {
    $success = false;
    try
    {
      switch ( $this->requestValues[0] )
      {
      case self::ACTION_ADD:
        $newLocation = $this->addLocation( $_GET['name'], $_GET['streetAddress'], $_GET['description'], $_GET['website'], $_GET['yelpId'] );
        $success = $newLocation !== false;
        $this->addOutput( $newLocation->getProperties( ) );
        break;
      case self::ACTION_DELETE:
        $success = $this->deleteLocation( $_GET['id'] );
        break;
      }
    }
    catch ( Exception $e )
    {
      $this->addOutput( 'error', $e->getMessage( ) );
    }
    $this->addOutput( 'result', $success );
    $this->output( );
    exit;
  }

  protected function addLocation( $name, $streetAddress, $description, $website, $yelpId )
  {
    $lc = new LocationCreator( );
    $lc->create( $name, $streetAddress, $description, $website, $yelpId );
    $lc->save( );
    return $lc;
  }

  protected function deleteLocation( $locationId )
  {
    $location = new LocationCreator( );
    $location->load( $locationId );
    return $location->delete( );
  }
}
