<?php

class LogoutHandler extends ApplicationBase
{
  const URL_SUCCESSFUL = '/';

  function execute( )
  {
    try
    {
      $this->logout( );
    }
    catch ( Exception $e )
    {
    }

    $this->redirect( self::URL_SUCCESSFUL );
  }

  function logout( )
  {
    $this->viewer->logout( ); 
  }
}
