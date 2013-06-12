<?php
require_once('../AutoLoader.php');
ini_set('display_errors', 1);

if ( $_POST['action'] === 'delete' )
{
	// first delete user from other stuff
	
	$query = sPDO::getInstance()->prepare('DELETE FROM messages WHERE sender_id = :user_id OR recipient_id = :user_id');
	$query->bindValue(':user_id', $_POST['userId'] );
	$query->execute();
	
	$query = sPDO::getInstance()->prepare('DELETE FROM location_attendees WHERE user_id = :user_id');
	$query->bindValue(':user_id', $_POST['userId'] );
	$query->execute();
	
	$query = sPDO::getInstance()->prepare('DELETE FROM logins WHERE user_id = :user_id');
	$query->bindValue(':user_id', $_POST['userId'] );
	$query->execute();
	
	$query = sPDO::getInstance()->prepare('DELETE FROM profile_field_values WHERE user_id = :user_id');
	$query->bindValue(':user_id', $_POST['userId'] );
	$query->execute();
	
	$query = sPDO::getInstance()->prepare('DELETE FROM friend_requests WHERE recipient_id = :user_id OR sender_id = :user_id');
	$query->bindValue(':user_id', $_POST['userId'] );
	$query->execute();
	
	$query = sPDO::getInstance()->prepare('DELETE FROM friends WHERE user_one = :user_id OR user_two = :user_id');
	$query->bindValue(':user_id', $_POST['userId'] );
	$query->execute();
	
	$query = sPDO::getInstance()->prepare('DELETE FROM photos WHERE user_id = :user_id');
	$query->bindValue(':user_id', $_POST['userId'] );
	$query->execute();
	
	$query = sPDO::getInstance()->prepare('DELETE FROM location_comment_replies WHERE user_id = :user_id');
	$query->bindValue(':user_id', $_POST['userId'] );
	$query->execute();
	
	$query = sPDO::getInstance()->prepare('DELETE FROM location_comments WHERE user_id = :user_id');
	$query->bindValue(':user_id', $_POST['userId'] );
	$query->execute();
	
	$query = sPDO::getInstance()->prepare('DELETE FROM location_shares WHERE recipient_id = :user_id OR sender_id = :user_id');
	$query->bindValue(':user_id', $_POST['userId'] );
	$query->execute();
	
	$query = sPDO::getInstance()->prepare('DELETE FROM featured_event_attendees WHERE user_id = :user_id');
	$query->bindValue(':user_id', $_POST['userId'] );
	$query->execute();
	
	$query = sPDO::getInstance()->prepare('DELETE FROM user_tag_prefs WHERE user_id = :user_id');
	$query->bindValue(':user_id', $_POST['userId'] );
	$query->execute();
	
	$query = sPDO::getInstance()->prepare('DELETE FROM user_quick_picks WHERE user_id = :user_id');
	$query->bindValue(':user_id', $_POST['userId'] );
	$query->execute();
	
	// now delete the user
  	$query = sPDO::getInstance()->prepare('DELETE FROM users WHERE user_id = :user_id');
  	$query->bindValue(':user_id', $_POST['userId'] );
  	$query->execute();
}

if ( $_POST['action'] === 'create' )
{
	
	$registration = new MemberRegistration( );
	$registration->setDetails( $_POST['fname'], $_POST['lname'],
			$_POST['email'], $_POST['birthday'],
			$_POST['gender'], $_POST['password'] );
	$registration->setCollege( $_POST['college'] );
	$registration->complete( );
}
?>