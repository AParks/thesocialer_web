<?php

class DOMGenerator
{
  public static function getNode( $dom, $element, $name )
  {
    $generator = new DOMGenerator( );
    return $generator->toNode( $dom, $element, $name );
  }

  public function toNode( $dom, $element, $name )
  {
    $node = $dom->createElement( $name );
    $this->toValue( $dom, $node, $name, $element ); 
    return $node;
  }

  protected function toValue( $dom, $node, $key, $value )
  {
    if ( is_int($key ) )
    {
      $key = $node->tagName;
    } 

    if ( is_scalar( $value ) )
    {
      $node->setAttribute( $key, $value);
    }
    elseif ( $value instanceof ATransformableObject )
    {
      $node->appendChild( $value->toNode( $dom ) );
    }
    elseif ( is_array( $value ) ) 
    {
      $element = $dom->createElement( $key );
      $node->appendChild( $element );
      foreach ( $value as $newKey => $newValue )
      {
        $this->toValue( $dom, $element, $newKey, $newValue );
      }
    }
    elseif ( get_class( $value ) === 'stdClass' )
    {
      $element = $dom->createElement( $key );
      $node->appendChild( $element );
      foreach ( (array) $value as $newKey => $newValue )
      {
        $this->toValue( $dom, $element, $newKey, $newValue );
      }
    }
  }
}
