<?php

abstract class JSONApplicationBase extends ApplicationBase
{
  private $output = array( );
  protected $viewer;

  public function __construct( ) {
    parent::__construct( );
    //header('Content-type: application/json');

    

     
    if ( get_magic_quotes_gpc( ) ) {
      foreach ( $_GET as $key => $value ) {
        $_GET[$key] = stripslashes( $value );
      }
    }

    $this->viewer = Viewer::getInstance( );
  }

  protected function addOutput( $key, $value = null ) {
    if ( is_array( $key ) ) {
      foreach ( $key as $k => $v ) {
        $this->output[$k] = $v;
      }
    }
    else {
      $this->output[$key] = $value;
    }
  }  

  protected function output( ) {
    header('Content-type: application/json');
    echo json_encode( $this->output );
  }

  public function getOutput( ) {
    return json_encode( $this->output );
  }
}
