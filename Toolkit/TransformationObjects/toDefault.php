<?php

abstract class toDefault implements ITransformationObject
{
  const PROPERTY_REQUIRED = 'required';
  const PROPERTY_DEFAULT = 'default';

  protected $attributes = array( );
  protected $attributeDeclarations = array( );
  protected $value;

  public function __construct( array $attributes, $value = null )
  {
    foreach ( $this->attributeDeclarations as $attribute => $properties )
    {
      if (isset($properties[self::PROPERTY_DEFAULT]) && (isset($attributes[$attribute]) === false))
      {
        $attributes[$attribute] = $properties[self::PROPERTY_DEFAULT];
      }
    }

    $this->attributes = $attributes;
    $this->value = $value;
  }

  public final function getMarkup( )
  {
    return $this->_getMarkup( );
  }

  abstract function _getMarkup();
}
