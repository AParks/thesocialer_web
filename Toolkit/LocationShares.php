<?php

class LocationShares {
  function __construct( ) { }

  public function getShares( Member $user ) {
    $locationShares = array( );

    $query = sPDO::getInstance( )->prepare( 'SELECT sender_id, location_id, location_date, EXTRACT(EPOCH FROM sent_at) FROM get_location_shares( :user_id )' );
    $query->bindValue( ':user_id', $user->userId );
    $query->execute( );
    foreach ( $query->fetchAll( PDO::FETCH_OBJ ) as $details ) {
      $locationShares[] = new LocationShare( new Member( $details->sender_id ), $user, new Location($details->location_id),  $details->date_part, $details->location_date );
    }

    return $locationShares;
  }


  public function sendShare( Member $sendingUser, Member $receivingUser, $location_id, $date ) {
    $query = sPDO::getInstance( )->prepare( 'SELECT new_location_share( :sender_id, :recipient_id, :location_id, :location_date )' );
    $query->bindValue( ':sender_id', $sendingUser->userId );
    $query->bindValue( ':recipient_id', $receivingUser->userId );
    $query->bindValue( ':location_id', $location_id );
    $query->bindValue( ':location_date', $date );

    return $query->execute( );
  }

  public function removeShare( Member $receivingUser, $location_id, $location_date ) {
    $query = sPDO::getInstance( )->prepare( 'SELECT delete_location_share( :recipient_id, :location_id, :location_date )' );
    $query->bindValue( ':recipient_id', $receivingUser->userId );
    $query->bindValue( ':location_id', $location_id );
    $query->bindValue( ':location_date', $location_date );
    $query->execute( );
    return $query->fetchColumn( 0 );
  }

}
