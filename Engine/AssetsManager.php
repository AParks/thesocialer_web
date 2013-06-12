<?php

class AssetsManager
{
  protected $css = array( );
  protected $javaScript = array( ); 
  protected $initJavaScript = array( );
  protected $autoLoader;

  private function __construct( )
  {
    $config = ConfigManager::getInstance( )->get( __CLASS__ );  
    $this->autoLoader = AutoLoader::getInstance( );

    foreach ( $config->JavaScript->file as $js )
    {
      $this->addJavaScript( strval( $js ), (bool) $js->attributes( )->isComplete );
    }

    foreach ( $config->CSS->file as $css )
    {
      $this->addCSS( strval( $css ), (bool) $css->attributes( )->isComplete );
    }
  }

  public static function getInstance( )
  {
    static $instance = null;

    if ( $instance === null )
    {
      $instance = new AssetsManager( );
    }

    return $instance;
  }

  public function addCSS( $fileName, $isComplete = false )
  {
    if ( $isComplete )
    {
      $this->css[] = $fileName;
    }
    elseif ( ( $file = $this->autoLoader->getFile( 'CSS', $fileName ) ) )
    {
      $this->css[] = $file;
    }
  }

  public function addJavaScript( $fileName, $isComplete = false )
  {
    if ( $isComplete )
    {
      $this->javaScript[] = $fileName;
    }
    elseif ( ( $file = $this->autoLoader->getFile( 'JavaScript', $fileName ) ) )
    {
      $this->javaScript[] = $file;
    }
  }

  public function addInitJavaScript( $script )
  {
    $this->initJavaScript[] = $script;
  }

  public function getInitJavaScript( )
  {
    return $this->initJavaScript;
  }
  
  public function getCSS( )
  {
    return $this->css;
  }

  public function getJavaScript( )
  {
    return $this->javaScript;
  }
}
