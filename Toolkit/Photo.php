<?php

class Photo extends ATransformableObject
{
  const DEFAULT_MALE_ID = -1;
  const DEFAULT_FEMALE_ID = -2;

  const SIZE_SMALL = 'Small';
  const SIZE_MEDIUM = 'Medium';
  const SIZE_LARGE = 'Large';

  const PATH_SIZE_SMALL = '/Photos/Small/';
  const PATH_SIZE_MEDIUM = '/Photos/Medium/';
  const PATH_SIZE_LARGE = '/Photos/Large/';

  const PATH_DEFAULT_MALE = '/Static/Images/default_male.gif';
  const PATH_DEFAULT_FEMALE = '/Static/Images/default_female.gif';

  protected $sizes = array( self::SIZE_SMALL, self::SIZE_MEDIUM, self::SIZE_LARGE );
  protected $pathMappings = array( self::SIZE_SMALL => self::PATH_SIZE_SMALL,
                                   self::SIZE_MEDIUM => self::PATH_SIZE_MEDIUM,
                                   self::SIZE_LARGE => self::PATH_SIZE_LARGE );

  
  protected $photoId;
  protected $userId;
  protected $isDefault;
  protected $paths = array( );
  protected $path;
  protected $addedAt;

  protected $publicProperties = array( 'photoId', 'userId', 'path', 'addedAt', 'paths', 'isDefault' );

  public function __construct( $photoId )
  {
    $this->photoId = (int) $photoId;

    if ( $this->photoId === self::DEFAULT_FEMALE_ID || $this->photoId === self::DEFAULT_MALE_ID )
    {
      $this->_loadDefault( );
    }
    else
    {
      $this->_load( );
    }
  }

  protected function _loadDefault( )
  {
    if ( $this->photoId === self::DEFAULT_FEMALE_ID )
    {
      foreach ( $this->sizes as $size )
      {
        $this->paths[$size] = self::PATH_DEFAULT_FEMALE; 
      }
    }
    else
    {
      foreach ( $this->sizes as $size )
      {
        $this->paths[$size] = self::PATH_DEFAULT_MALE; 
      }
    }
  }

  protected function _load( )
  {
    $pdo = sPDO::getInstance( );
    $query = $pdo->prepare( 'SELECT user_id, is_default, path, added_at FROM get_photo( :photo_id ) WHERE user_id IS NOT NULL' );
    $query->bindValue( ':photo_id', $this->photoId );
    $query->execute( ); 

    $row = $query->fetch( PDO::FETCH_OBJ );

    $this->path = $row->path;
    $this->isDefault = $row->is_default;

    $this->addedAt = $row->added_at;

    foreach ( $this->sizes as $size )
    {
      $this->paths[$size] = $this->pathMappings[$size] . $this->path;
    }

    $this->userId = $row->user_id;
  }

  public static function getByUser( $userId, $gender )
  {
    $pdo = sPDO::getInstance( );
    $query = $pdo->prepare( 'SELECT photo_id, path, added_at FROM get_member_photo( :user_id ) WHERE photo_id IS NOT NULL' );
    $query->bindValue( ':user_id', $userId );
    $query->execute( ); 

    $row = $query->fetch( PDO::FETCH_OBJ );

    if ( $row )
    {
      return new Photo( $row->photo_id );
    }
    
    if ( $gender === Member::GENDER_MALE )
    {
      return new Photo( self::DEFAULT_MALE_ID );
    }

    return new Photo( self::DEFAULT_FEMALE_ID );
  }

  public function getFullPath($size)
  {
    return $_SERVER['DOCUMENT_ROOT'] . $this->paths[$size]; 
  }

  public static function create( Member $user, $path )
  {
    $pdo = sPDO::getInstance( );
    $query = $pdo->prepare( 'SELECT add_photo( :user_id, :path )' );
    $query->bindValue(':user_id', $user->userId );
    $query->bindValue(':path', $path);
    $query->execute( );
    return new Photo( $query->fetchColumn( 0 ) );
  }

  public function reThumbnail($x, $y, $width, $height)
  {
    $photoUploader = new PhotoUploader(); 
    $photoUploader->setThumbnail($this->getFullPath(self::SIZE_LARGE), $this->getFullPath(self::SIZE_SMALL), 
                                 $x, $y, $width, $height,
                                 PhotoUploader::WIDTH_SMALL, PhotoUploader::HEIGHT_SMALL); 
    $photoUploader->setThumbnail($this->getFullPath(self::SIZE_LARGE), $this->getFullPath(self::SIZE_MEDIUM),
                                 $x, $y, $width, $height,
                                 PhotoUploader::WIDTH_MEDIUM, PhotoUploader::HEIGHT_MEDIUM); 
    return true; 
  }
}
