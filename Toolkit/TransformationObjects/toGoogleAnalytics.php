<?php

class toGoogleAnalytics extends toDefault
{
  const ATTR_ENVIRONMENT = 'environment';

  protected $attributeDeclarations =
    array( self::ATTR_ENVIRONMENT => array( self::PROPERTY_REQUIRED => false, self::PROPERTY_DEFAULT => GoogleAnalytics::ENVIRONMENT_PRODUCTION ),
      );

  public function _getMarkup()
  {
    $ga = new GoogleAnalytics( $this->attributes[self::ATTR_ENVIRONMENT]);
    return $ga->getCode( );
  }
}
