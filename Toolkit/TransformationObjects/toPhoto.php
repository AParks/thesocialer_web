<?php

class toPhoto extends toDefault
{
  const ATTR_USER_ID = 'userId';
  const ATTR_SIZE = 'size';
  const ATTR_CLASS = 'class';
  const ATTR_LINK = 'link';
  const ATTR_INCLUDE_LARGE = 'include-large';

  const SIZE_LARGE = 'Large';

  const PHOTO_PATH = '<img src="/photo/%s/%s" class="%s" />';
  const PHOTO_PATH_LINKED = '<a href="/profile/%s"><img src="/photo/%s/%s" class="%s" /></a>';

  protected $attributeDeclarations =
    array( self::ATTR_USER_ID => array( self::PROPERTY_REQUIRED => true ),
           self::ATTR_SIZE    => array( self::PROPERTY_REQUIRED => false, self::PROPERTY_DEFAULT => self::SIZE_LARGE ),
           self::ATTR_CLASS   => array( self::PROPERTY_REQUIRED => false ),
           self::ATTR_INCLUDE_LARGE    => array( self::PROPERTY_REQUIRED => false, self::PROPERTY_DEFAULT => 'false' ),
           self::ATTR_LINK    => array( self::PROPERTY_REQUIRED => false, self::PROPERTY_DEFAULT => 'false' ),
    );

  public function _getMarkup()
  {
    $return = sprintf( '<img src="/photo/%s/%s" class="%s"',
      $this->attributes[self::ATTR_USER_ID],
      $this->attributes[self::ATTR_SIZE],
      $this->attributes[self::ATTR_CLASS]);


    if ( $this->attributes[self::ATTR_INCLUDE_LARGE] === 'true' )
    {
      $return .= sprintf(' data-large="/photo/%s/Large"', $this->attributes[self::ATTR_USER_ID]);
    }

    $return .= ' />';

    if ( $this->attributes[self::ATTR_LINK] === 'true' )
    {
      $return = sprintf('<a href="/profile/%s">', $this->attributes[self::ATTR_USER_ID])
        . $return . '</a>';
    }

    return $return;
  }
}
