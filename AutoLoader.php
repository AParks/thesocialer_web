<?php
class AutoLoader  {
  protected $files = array( );

  public function getInstance( ) {
    static $instance;

    if ( $instance === null ) {
      $instance = new AutoLoader( );
    }

    return $instance;
  }

  function __construct( ) {
    $this->readDirectory( $_SERVER['DOCUMENT_ROOT'] . '/', $this->files );
  }

  protected function readDirectory( $directory, & $files ) {
    $handle = opendir( $directory );

    while ( ( $file = readdir( $handle ) ) !== false ) {
      if ( $file[0] === '.' ) {
        continue;
      }

      $file = $directory . $file;

      if ( is_file( $file ) ) {
        $pieces = explode( '.', baseName( $file ) );
        $ext = array_pop( $pieces );
        $fileName = implode( '.', $pieces );

        switch ( $ext ) {
        case 'php':
          $files['PHP'][$fileName] = $file; 
          break;
        case 'xsl':
          $files['XSL'][$fileName] = $file;
          break;
        case 'js':
          $files['JavaScript'][$fileName] = str_replace($_SERVER['DOCUMENT_ROOT'], '', ltrim( $file, '.' ));
          break;
        case 'css':
          $files['CSS'][$fileName] = str_replace($_SERVER['DOCUMENT_ROOT'], '', ltrim( $file, '.' ));
          break;
        case 'xml':
          $files['XML'][$fileName] = $file;
          break;
        }
      }
      elseif ( is_dir( $file ) ) {
        $this->readDirectory( $file . '/', $files );
      }
    }
  }

  public function getFile( $type, $fileName ) {
    return $this->files[$type][$fileName];
  }
}

function __autoload( $class ) {
  $file = AutoLoader::getInstance( )->getFile( 'PHP', $class );

  if ( ! $file ) {
    throw new Exception( 'Unknown object: ' . $class );
  }

  require( $file );
}

