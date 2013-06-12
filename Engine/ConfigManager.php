<?php

class ConfigManager
{
  private $configs = array( );
  private $autoLoader;

  private function __construct( )
  {
    $this->autoLoader = AutoLoader::getInstance( );
  }

  public function getInstance( )
  {
    static $instance;

    if ( $instance === null )
    {
      $instance = new ConfigManager( );
    }

    return $instance;
  }

  public function get( $name )
  {
    if ( isset( $this->configs[ $name ] ) === false )
    {
      $this->load( $name );
    }

   return $this->configs[ $name ]; 
  }

  private function load( $name )
  {
    $file = $this->autoLoader->getFile( 'XML', $name );
    $this->configs[$name] = simplexml_load_file( $file );
  }
}
