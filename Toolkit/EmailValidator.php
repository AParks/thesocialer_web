<?php

class EmailValidator
{
  public static function validate( $emailAddress )
  {
    return preg_match( '^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+).*(\.[a-z]{2,3})$', $emailAddress, $matches );
  }
}
