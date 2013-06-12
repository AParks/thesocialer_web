<?php

class Location extends ATransformableObject {
  protected $id;
  protected $name;
  protected $streetAddress;
  protected $city = 1; // @todo Needs to support more than one city
  protected $cityName = 'Philadelphia';
  protected $stateName = 'Pennsylvania';
  protected $description;
  protected $website;
  protected $yelpId;
  protected $latitude;
  protected $longitude;
  protected $image;
  protected $tags = array();
  protected $likes;
  protected $userLikes;

  protected $publicProperties = array( 'id', 'name', 'streetAddress', 'description', 'website', 'yelpId', 'cityName', 'stateName', 'latitude', 'longitude', 'image', 'tags', 'likes', 'userLikes' );

  public function __construct( $id ) {
    $this->id = $id;
    $this->_load( );
  }

  public function getPublicProperties( ) {
    $properties = array( );

    foreach ( $this->publicProperties as $property ) {
      if ( is_scalar( $this->$property ) === true ) {
        $properties[$property] = $this->$property;
      }
    }
    
    return $properties;
  }

  protected function _load( ) {
    $pdo = sPDO::getInstance( );
    $query = $pdo->prepare( 'SELECT location_id, city_id, location_name, street_address, description, website, yelp_id, latitude, longitude, location_image FROM get_location( :id )' );
    $query->bindParam ( ':id', $this->id );
    
    if ( ! $query->execute( ) ) {
      throw new Exception( 'Query.' . print_r( $query->errorInfo( ), true ) );
    } 

    $row = $query->fetch( PDO::FETCH_OBJ );
    
    if ( ! $row ) {
      throw new Exception( 'Unknown Location.' );
    }
    
    $tagquery = $pdo->prepare( 'SELECT t.tag_id, t.tag_description FROM tags t, location_tags lt WHERE t.tag_id = lt.tag_id AND lt.location_id = :loc_id');
    $tagquery->bindValue(':loc_id', $this->id );
    $tagquery->execute();

    foreach ( $tagquery->fetchAll(PDO::FETCH_ASSOC) as $tag ) {
      $this->tags[$tag['tag_id']] = $tag['tag_description'];
    }
    $this->likes = $this->getLikeCount();
    $v = Viewer::getInstance()->user;
    $this->userLikes = $v ? $v->likesLocation($this->id) : false;
    // city and yelpid were commented out because they're not being used and they caused a php warning in the error log
    // feel free to reenable them once you get cities or yelp integrated
    $this->name = $row->location_name;
    $this->streetAddress = $row->street_address;
    $this->description = $row->description;
    //$this->city = $row->city;
    $this->website = $row->website;
    //$this->yelpId = $row->yelpId;
    $this->latitude = $row->latitude;
    $this->longitude = $row->longitude;
    $this->image = $row->location_image;
  }
	
  public function getLikeCount() {
    $pdo = sPDO::getInstance( );
    $likequery = $pdo->prepare( 'SELECT COUNT(ll.user_id) FROM location_likes ll WHERE ll.location_id = :loc_id');
    $likequery->bindValue(':loc_id', $this->id );
    $likequery->execute();
    $num = $likequery->fetch(PDO::FETCH_NUM);
    
    return $num[0];
  }

  public function getLikes() {
    $pdo = sPDO::getInstance( );
    $likequery = $pdo->prepare( 'SELECT ll.user_id FROM location_likes ll WHERE ll.location_id = :loc_id');
    $likequery->bindValue(':loc_id', $this->id );
    $likequery->execute();
    $num = $likequery->fetch(PDO::FETCH_NUM);
    
    return $num;
  }
  
  public function verifyLocation( ) {
    if ( is_string( $this->name ) === false || $this->name === '' || strlen( $this->name ) > 150 ) {
      throw new Exception( 'Invalid name.' );
    } 
    
    if ( is_string( $this->streetAddress ) === false || $this->streetAddress === '' || strlen( $this->streetAddress ) > 150 ) {
      throw new Exception( 'Invalid street address.' );
    }

    if ( is_string( $this->description ) === false || strlen( $this->description ) > 1000 ) {
      throw new Exception( 'Invalid description.' );
    }

    if ( is_string( $this->website ) === false || strlen( $this->website ) > 100 ) {
      throw new Exception( 'Invalid website.' );
    }
  }
}
