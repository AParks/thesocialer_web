<?php

class LocationCreator extends Location 
{
  public function __construct( ) 
  {

  }

  public function create( $name, $streetAddress, $description, $website, $yelpId )
  {
    $this->name = $name;
    $this->streetAddress = $streetAddress;
    $this->description = $description;
    $this->website = $website;
    $this->yelpId = $yelpId;
  }

  public function load( $id )
  {
    $this->id = $id;
    $this->_load( );
  }

  public function save( )
  {
    $this->verifyLocation( );

    if ( $this->id !== null )
    {
      // Update.
    }
    else
    {
      //Insert. Set id.
      $this->id = $this->insert( );
      $this->_load( );
    }

    return $this->id;
  }

  protected function insert( )
  {
    $pdo = sPDO::getInstance( );
    $query = $pdo->prepare( 'SELECT insert_location( :cityId, :locationName, :streetAddress, :description, :website, :yelpId );' );
    $query->bindParam( ':cityId', $this->city );
    $query->bindParam( ':locationName', $this->name );
    $query->bindParam( ':streetAddress', $this->streetAddress );
    $query->bindParam( ':description', $this->description );
    $query->bindParam( ':website', $this->website );
    $query->bindValue( ':yelpId', $this->yelpId ? $this->yelpId : PDO::PARAM_NULL );
    $result = $query->execute( );

    if ( $result === false )
    {
      throw new sPDOException( print_r( $query->errorInfo( ), true ) );
    }

    return $query->fetchColumn( 0 );
  }

  public function delete( )
  {
    $pdo = sPDO::getInstance( );
    $query = $pdo->prepare( 'SELECT delete_location( :id )' );
    $query->bindParam( ':id', $this->id );
    $result = $query->execute( );

    if ( ! $result )
    {
      throw new sPDOException( print_r( $query->errorInfo( ), true ) );
    }

    return $result;
  }
}
