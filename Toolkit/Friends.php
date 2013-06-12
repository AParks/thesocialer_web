<?php

class Friends {
  function __construct( ) { }

  function get( Member $user ) {
    $query = sPDO::getInstance( )->prepare( 'SELECT user_id FROM get_friends( :user_id ) order by random() Limit 10' );
    $query->bindValue( ':user_id', $user->userId );
    $query->execute( );
    return $query->fetchAll( PDO::FETCH_COLUMN, 0 );
  }

  function areFriends( Member $viewer, Member $user ) {
    return in_array( $user->userId, $this->get( $viewer ) );
  }
}
