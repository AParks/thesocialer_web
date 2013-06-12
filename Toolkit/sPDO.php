<?php

class sPDO
{
  private function __construct( )
  {
  } 

  public static function getInstance( )
  {
    static $instance;

    if ( $instance === null )
    {
      try {
        $instance = new pdo( 'pgsql:host=localhost;port=5432;user=mayosala_socialer;password=socialer;dbname=mayosala_socialer;' );
        $instance->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        //echo "PDO connection object created";
      }
      catch(PDOException $e)
      {
        echo $e->getMessage();
      }
      
    }

    return $instance;
  }
}
