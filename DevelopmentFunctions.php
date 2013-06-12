<?php

class DevelopmentFunctions
{
  static function outputXML( $dom, $node )
  {
    $dom->formatOutput = true;
    echo '<textarea style="width: 400px; height: 300px;">' . $dom->saveXML( $node ) . '</textarea>';
  }

  static function trace( )
  {
    foreach ( array_slice( debug_backtrace( ), 0, 4 ) as $db )
    {
      echo $db['file'] . ':' . $db['line'] . "<br />\n";
    }
  }
}
