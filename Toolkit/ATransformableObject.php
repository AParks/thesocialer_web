<?php

abstract class ATransformableObject {
  protected $publicProperties = array( ); 

  public function __get( $key ) {
    if ( in_array( $key, $this->publicProperties ) ) {
      return $this->{$key};
    }

    throw new Exception( sprintf( 'Unknown property: %s', $key ) );
  }

  public function toNode( $dom ) {
    $node = $dom->createElement( get_class( $this ) );

    foreach ( $this->publicProperties as $property ) {
      $this->toValue( $dom, $node, $property, $this->$property );
    }

    return $node;
  }

  protected function toValue( $dom, & $node, $key, $value ) {
   
    if ( is_scalar( $value ) ) {
      // can't have an attribute that starts with a number, so prepend an 'a'
      if (is_numeric($key)) {
      	$node->setAttribute( 'a'.$key, $value);
      }
      else {
          if($key === 'description'){
              error_log("key: " . $key . " value: " .  $value);
          }
      	$node->setAttribute( $key, $value);

      }
    }
    elseif ( $value instanceof ATransformableObject ) {
      $node->appendChild( $value->toNode( $dom ) );
    }
    elseif ( is_array( $value ) ) {
      $element = $dom->createElement( $key );
      foreach ( $value as $newKey => $newValue ) {
        $this->toValue( $dom, $element, $newKey, $newValue );
      }
      $node->appendChild( $element );
    }
  }

  public function getProperties( ) {
    $properties = array( );

    foreach ( $this->publicProperties as $property ) {
      $properties[$property] = $this->$property;
    }

    return $properties;
  }
}
