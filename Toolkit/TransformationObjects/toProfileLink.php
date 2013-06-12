<?php

class toProfileLink extends toDefault
{
  const ATTR_USER_ID = 'userId';

  const PROFILE_LINK = '<a href="/profile/%s">%s</a>'; 

  protected $attributeDeclarations =
    array( self::ATTR_USER_ID => array( self::PROPERTY_REQUIRED => true ),
    );

  public function _getMarkup()
  {
    return sprintf(self::PROFILE_LINK,
      $this->attributes[self::ATTR_USER_ID],
      $this->getLinkText( ));
  }

  protected function getLinkText()
  {
    if ( $this->value !== '' )
    {
      return $this->value;
    }

    $user = new Member( $this->attributes[self::ATTR_USER_ID] );
    return $user->firstName . ' ' . $user->lastName;
  }
}
