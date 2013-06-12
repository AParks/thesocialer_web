<?php

class FriendRequests
{
  function __construct( )
  {

  }

  public function getRequests( Member $user )
  {
    $friendRequests = array( );

    $query = sPDO::getInstance( )->prepare( 'SELECT sender_id, EXTRACT(EPOCH FROM sent_at) FROM get_friend_requests( :user_id )' );
    $query->bindValue( ':user_id', $user->userId );
    $query->execute( );
    foreach ( $query->fetchAll( PDO::FETCH_OBJ ) as $details )
    {
      $friendRequests[] = new FriendRequest( new Member( $details->sender_id ), $user, $details->date_part );
    }

    return $friendRequests;
  }

  public function sendRequest( Member $sendingUser, Member $receivingUser )
  {
    $query = sPDO::getInstance( )->prepare( 'SELECT new_friend_request( :sender_id, :recipient_id )' );
    $query->bindValue( ':sender_id', $sendingUser->userId );
    $query->bindValue( ':recipient_id', $receivingUser->userId );

    return $query->execute( );
  }

  public function removeRequest( Member $sendingUser, Member $receivingUser )
  {
    $query = sPDO::getInstance( )->prepare( 'SELECT delete_friend_request( :recipient_id, :sender_id )' );
    $query->bindValue( ':recipient_id', $receivingUser->userId );
    $query->bindValue( ':sender_id', $sendingUser->userId );
    $query->execute( );
    return $query->fetchColumn( 0 );
  }

  public function acceptRequest( Member $sendingUser, Member $receivingUser )
  {
    $query = sPDO::getInstance( )->prepare( 'SELECT accept_friend_request( :recipient_id, :sender_id )' );
    $query->bindValue( ':recipient_id', $receivingUser->userId );
    $query->bindValue( ':sender_id', $sendingUser->userId );
    return $query->execute( );
  }
}
