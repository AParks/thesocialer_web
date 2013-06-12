<?php

class LocationComments {

  function __construct( ) { }

  public function getCommentsForLocationAndDate( $locationid, $date ) {
    $locationComments = array( );

    $query = sPDO::getInstance( )->prepare( 'SELECT comment_id, user_id, location_id, location_name, location_date, EXTRACT(EPOCH FROM posted_at), message FROM get_location_comments( :location_id, :location_date ) ORDER BY posted_at DESC' );
    $query->bindValue( ':location_id', $locationid );
    $query->bindValue( ':location_date', $date );
    $query->execute( );
    foreach ( $query->fetchAll( PDO::FETCH_OBJ ) as $details ) {
      //error_log(print_r($details, true));
      $locationComments[] = new LocationComment( $details->comment_id, new Member( $details->user_id ), $details->location_id, $details->location_name, $details->location_date, $details->date_part, $details->message );
    }

    return $locationComments;
  }
  
  public function getTopCommentsForLocationAndDate( $loc, $date, $limit ) {
  	$locationComments = array( );
  	// use CURRENT_DATE if you just want for today
  	$querytext = 'SELECT lc.comment_id, lc.user_id, lc.location_id, loc.location_name, lc.location_date, EXTRACT(EPOCH FROM lc.posted_at), lc.message, COUNT(lc.comment_id) '
        .'FROM location_comment_likes lcl, location_comments lc, locations loc '
        .'WHERE lcl.comment_id = lc.comment_id AND lc.location_date = :date AND lc.location_id = loc.location_id AND loc.location_id = :loc_id '
        .'GROUP BY lc.comment_id, lc.user_id, lc.location_id, loc.location_name, lc.location_date, lc.posted_at, lc.message ORDER BY COUNT(lc.comment_id) DESC '
        .'LIMIT :limit';
  	$query = sPDO::getInstance( )->prepare( $querytext );
	$query->bindValue( ':loc_id', $loc );
  	$query->bindValue( ':date', $date );
  	$query->bindValue( ':limit', $limit );
  	$query->execute( );
  	foreach ( $query->fetchAll( PDO::FETCH_OBJ ) as $details ) {
  		$locationComments[] = new LocationComment( $details->comment_id, new Member( $details->user_id ), $details->location_id, $details->location_name, $details->location_date, $details->date_part, $details->message );
  	}
  
  	return $locationComments;
  }
  
  public function getRecentCommentsForDate( $date, $limit ) {
  	$locationComments = array( );
  
  	// use CURRENT_DATE if you just want for today
  	$querytext = 'SELECT lc.comment_id, lc.user_id, lc.location_id, loc.location_name, lc.location_date, EXTRACT(EPOCH FROM lc.posted_at), lc.message '
  	.'FROM location_comments lc, locations loc '
  	.'WHERE loc.location_id = lc.location_id AND lc.location_date = :date '
  	.'ORDER BY lc.posted_at DESC '
  	.'LIMIT :limit';
  	$query = sPDO::getInstance( )->prepare( $querytext );
  	$query->bindValue( ':date', $date );
  	$query->bindValue( ':limit', $limit );
  	$query->execute( );
  	foreach ( $query->fetchAll( PDO::FETCH_OBJ ) as $details ) {
  		$locationComments[] = new LocationComment( $details->comment_id, new Member( $details->user_id ), $details->location_id, $details->location_name, $details->location_date, $details->date_part, $details->message );
  	}
  
  	return $locationComments;
  }

  public function makeComment( Member $sendingUser, $location_id, $date, $message ) {
    $query = sPDO::getInstance( )->prepare( 'SELECT new_location_comment( :user_id, :location_id, :location_date, :message )' );
    $query->bindValue( ':user_id', $sendingUser->userId );
    $query->bindValue( ':location_id', $location_id );
    $query->bindValue( ':location_date', $date );
    $query->bindValue( ':message', $message );

    return $query->execute( );
  }
  
  public function deleteComment( Member $user, $comment_id ) {
  	$query = sPDO::getInstance( )->prepare( 'DELETE FROM location_comments WHERE comment_id = :comment_id AND user_id = :user_id' );
  	$query->bindValue( ':comment_id',  $comment_id);
  	$query->bindValue( ':user_id', $user->userId );
  	$query->execute( );
  }
  
  public function likeComment( Member $sendingUser, $comment_id ) {
  	$query = sPDO::getInstance( )->prepare( 'SELECT new_location_comment_like( :user_id, :comment_id )' );
  	$query->bindValue( ':user_id', $sendingUser->userId );
  	$query->bindValue( ':comment_id', $comment_id );
  
  	return $query->execute( );
  }
  public function unlikeComment( Member $sendingUser, $comment_id ) {
  	$query = sPDO::getInstance( )->prepare( 'SELECT del_location_comment_like( :user_id, :comment_id )' );
  	$query->bindValue( ':user_id', $sendingUser->userId );
  	$query->bindValue( ':comment_id', $comment_id );
  	return $query->execute( );
  }

  
  
  public function makeReply( Member $sendingUser, $comment_id, $message ) {
  	$query = sPDO::getInstance( )->prepare( 'SELECT new_location_comment_reply( :user_id, :comment_id, :message )' );
  	$query->bindValue( ':user_id', $sendingUser->userId );
  	$query->bindValue( ':comment_id', $comment_id );
  	$query->bindValue( ':message', $message );
  
  	return $query->execute( );
  }
  
  public function deleteReply( $user, $reply_id ) {
  	$query = sPDO::getInstance( )->prepare( 'DELETE FROM location_comment_replies WHERE reply_id = :reply_id AND user_id = :user_id' );
  	 $query->bindValue( ':user_id', $user->userId );
  	$query->bindValue( ':reply_id', $reply_id);
  	return $query->execute( );
  }

}
