<?php

class EventShares
{
  function __construct( )
  {

  }

  public function getShares( Member $user )
  {
    $eventShares = array( );

    $query = sPDO::getInstance( )->prepare( 'SELECT sender_id, event_id, EXTRACT(EPOCH FROM sent_at) FROM get_event_shares( :user_id )' );
    $query->bindValue( ':user_id', $user->userId );
    $query->execute( );
    foreach ( $query->fetchAll( PDO::FETCH_OBJ ) as $details )
    {
      $eventShares[] = new EventShare( new Member( $details->sender_id ), $user, $details->sent_at, $details->event_id );
    }

    return $eventShares;
  }

  public function sendShare( Member $sendingUser, Member $receivingUser, $event_id )
  {
    $query = sPDO::getInstance( )->prepare( 'SELECT new_event_share( :sender_id, :recipient_id, :event_id )' );
    $query->bindValue( ':sender_id', $sendingUser->userId );
    $query->bindValue( ':recipient_id', $receivingUser->userId );
    $query->bindValue( ':event_id', $event_id );

    return $query->execute( );
  }

  public function removeShare( Member $receivingUser, $event_id )
  {
    $query = sPDO::getInstance( )->prepare( 'SELECT delete_event_share( :recipient_id, :event_id )' );
    $query->bindValue( ':recipient_id', $receivingUser->userId );
    $query->bindValue( ':event_id', $event_id );
    $query->execute( );
    return $query->fetchColumn( 0 );
  }

  /*public function acceptRequest( Member $sendingUser, Member $receivingUser )
  {
    $query = sPDO::getInstance( )->prepare( 'SELECT accept_friend_request( :recipient_id, :sender_id )' );
    $query->bindValue( ':recipient_id', $receivingUser->userId );
    $query->bindValue( ':sender_id', $sendingUser->userId );
    return $query->execute( );
  }*/
}
