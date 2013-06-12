<?php

class NetworkJSON extends JSONApplicationBase {
  const ACTION_REQUEST = 'request';
  const ACTION_ACCEPT = 'accept';
  const ACTION_DECLINE = 'decline';
  const ACTION_GET = 'get';
  const ACTION_GET_ALL = 'getall';
  const ACTION_SEND_SHARE = 'share';
  const ACTION_SEND_SHARE_ALL = 'shareall';
  const ACTION_DISMISS_SHARE = 'dismissshare';
  const ACTION_ADD_USER_TAG = 'addtag';
  const ACTION_DELETE_USER_TAG = 'deltag';
  const ACTION_GET_COMMENTS = 'getcomments';
  const ACTION_MAKE_COMMENT = 'makecomment';
  const ACTION_DELETE_COMMENT = 'deletecomment';
  const ACTION_MAKE_REPLY = 'makereply';
  const ACTION_DELETE_REPLY = 'deletereply';
  const ACTION_LIKE_COMMENT = 'likecomment';
  const ACTION_UNLIKE_COMMENT = 'unlikecomment';
  const ACTION_GET_TOP_COMMENTS = 'gettopcomments';
  const ACTION_GET_RECENT_COMMENTS = 'getrecentcomments';
  const ACTION_LIKE_LOCATION = 'likelocation';
  const ACTION_UNLIKE_LOCATION = 'unlikelocation';

  function execute( ) {
  	
    $result = false;
  	
    switch ( $this->requestValues[0] ) {
    case self::ACTION_REQUEST:
      $result = $this->requestFriendship( $this->viewer->user, new Member( $_GET['userId'] ) ); 
      break;
    case self::ACTION_ACCEPT:
      $result = $this->acceptFriendship( new Member( $_GET['userId'] ), $this->viewer->user );
      break;
    case self::ACTION_DECLINE:
      $result = $this->declineFriendship( new Member( $_GET['userId'] ), $this->viewer->user );
      break;
    case self::ACTION_GET:
      $result = $this->getFriends( $this->viewer->user );
      break;
    case self::ACTION_GET_ALL:
      $result = $this->getAllUsers();
      break;
    case self::ACTION_SEND_SHARE:
      $result = $this->sendShare( $this->viewer->user, new Member( $_GET['userId'] ), $_GET['locationId'], $_GET['locationDate'] );
      break;
    case self::ACTION_SEND_SHARE_ALL:
      $result = $this->shareToAllAs( $this->viewer->user, $_GET['locationId'], $_GET['locationDate']);
      break;
    case self::ACTION_DISMISS_SHARE:
      $result = $this->dismissShare( $this->viewer->user, $_GET['locationId'], $_GET['locationDate'] );
      break;
    case self::ACTION_ADD_USER_TAG:
      $result = $this->addUserTag( $this->viewer->user, $_GET['tags'] );
      break;
    case self::ACTION_DELETE_USER_TAG:
      $result = $this->deleteUserTag( $this->viewer->user, $_GET['tags']);
      break;
    case self::ACTION_GET_COMMENTS:
      $result = $this->getComments( $_GET['location'], $_GET['date']);
      break;
    case self::ACTION_MAKE_COMMENT:
      $result = $this->makeComment( $this->viewer->user, $_GET['location'], $_GET['date'], $_GET['message'] );
      break;
    case self::ACTION_MAKE_REPLY:
      $result = $this->makeReply( $this->viewer->user, $_GET['commentId'], $_GET['message']);
      break;
    case self::ACTION_LIKE_COMMENT:
      $result = $this->likeComment( $this->viewer->user, $_GET['commentId']);
      break;
    case self::ACTION_UNLIKE_COMMENT:
      $result = $this->unlikeComment( $this->viewer->user, $_GET['commentId']);
      break;
    case self::ACTION_GET_TOP_COMMENTS:
      $result = $this->getTopComments( $_GET['location'], $_GET['date'], $_GET['limit'] );
      break;
    case self::ACTION_GET_RECENT_COMMENTS:
      $result = $this->getRecentComments( $_GET['date'], $_GET['limit'] );
      break;
    case self::ACTION_LIKE_LOCATION:
      $result = $this->likeLocation( $_GET['locationId'] );
      break;
    case self::ACTION_UNLIKE_LOCATION:
      $result = $this->unlikeLocation( $_GET['locationId'] );
      break;
    case self::ACTION_DELETE_COMMENT:
      $result = $this->deleteComment( $this->viewer->user, $_GET['commentId']);
      break;
    case self::ACTION_DELETE_REPLY:
      $result = $this->deleteReply( $_GET['replyId']);
      break;  
      
    }

    $this->addOutput('success', $result);
    $this->output();
    exit;
  }
  
  function requestFriendship( Member $sender, Member $recipient ) {
    $friendRequests = new FriendRequests( );
    try {
      $friendRequests->sendRequest( $sender, $recipient );
      $result = true;
    }
    catch ( Exception $e ) {
      $result = false;
    }
    
    return $result;
  }

  function declineFriendship( Member $sender, Member $recipient ) {
    $friendRequests = new FriendRequests( );
    return $friendRequests->removeRequest( $sender, $recipient );
  }
  
  function acceptFriendship( Member $sender, Member $recipient ) {
    $friendRequests = new FriendRequests( );
    return $friendRequests->acceptRequest( $sender, $recipient );
  }
  
  function getFriends( Member $user ) {
    $friends = new Friends();
    $friendInfo = array();

    foreach ( $friends->get($user) as $userId ) {
      $friend = new Member( $userId );
      $friendInfo[] = $friend->getProperties(); 
    }
    
    $this->addOutput( 'friends', $friendInfo );
    
    return true;
  }
  
  // This will be really bad if we ever have a ton of users
  // We'll need to rethink sharing if this gets out of hand
  function getAllUsers( ) {
    $users = new UserSearch();
    $otherUserInfo = array();
    foreach ( $users->query("") as $otherUser ) {
      $otherUserInfo[] = $otherUser->getProperties();
    }
    $this->addOutput( 'friends', $otherUserInfo );
    return true;
  }
  
  function shareToAllAs(Member $sendingUser, $locationId, $locationDate) {
    // only allows user 193 to send to all, for security purposes
    // right now user 193 is the official socialer account
    if($sendingUser->userId == 193) {
      $users = new UserSearch();
      $locationShares = new LocationShares( );
      $otherUserInfo = array();
      foreach ( $users->query("") as $otherUser ) {
	$locationShares->sendShare( $sendingUser, $otherUser, $locationId, $locationDate );
      }
      return true;
    }
    return false;
  }  
  
  function sendShare( Member $sender, Member $recipient, $locationId, $locationDate ) {
    $locationShares = new LocationShares( );
    try {
      $locationShares->sendShare( $sender, $recipient, $locationId, $locationDate );
      $result = true;
    }
    catch ( Exception $e ) {
      $this->addOutput( 'e', $e->getMessage() );
      $result = false;
    }
    return $result;
  }
  
  function dismissShare(Member $recipient, $locationId, $locationDate ) {
    $locationShares = new LocationShares( );
    try {
      $locationShares->removeShare($recipient, $locationId, $locationDate );
      $result = true;
    }
    catch ( Exception $e ) {
      $this->addOutput( 'e', $e->getMessage() );
      $result = false;
    }
    return $result;
  }
  
  function addUserTag( Member $user, $tagDesc ) {
    try {
      $user->addTag($tagDesc);
      $result = true;
    }
    catch ( Exception $e ) {
      $this->addOutput( 'e', $e->getMessage() );
      $result = false;
    }
    return $result;
  }
  
  function deleteUserTag( Member $user, $tagDesc ) {
    try {
      $user->delTag($tagDesc);
      $result = true;
    }
    catch ( Exception $e ) {
      $this->addOutput( 'e', $e->getMessage() );
      $result = false;
    }
    return $result;
  }
  
  function getComments ( $locationid, $date ) {
    $locationComments = new LocationComments();
    try {
      $commentdata = $locationComments->getCommentsForLocationAndDate($locationid, $date);
      $comments = array();
      foreach ( $commentdata as $comment ) {
	$comments[] = $comment->getProperties();
	//error_log(print_r($comment->getProperties(), true));
      }
      $this->addOutput( 'comments', $comments );
      //error_log(print_r($comments, true));
      $result = true;
    }
    catch ( Exception $e ) {
      $this->addOutput( 'e', $e->getMessage() );
      $result = false;
    }
    return $result;
  }
  
  function getTopComments ( $loc, $date, $limit ) {
    $locationComments = new LocationComments();
    try {
      $commentdata = $locationComments->getTopCommentsForLocationAndDate($loc, $date, $limit);
      $comments = array();
      foreach ( $commentdata as $comment ) {
	$comments[] = $comment->getProperties();
	//error_log(print_r($comment->getProperties(), true));
      }
      $this->addOutput( 'comments', $comments );
      
      $result = true;
    }
    catch ( Exception $e ) {
      $this->addOutput( 'e', $e->getMessage() );
      $result = false;
    }
    return $result;
  }
  
  function getRecentComments ( $date, $limit ) {
    $locationComments = new LocationComments();
    try {
      $commentdata = $locationComments->getRecentCommentsForDate($date, $limit);
      $comments = array();
      foreach ( $commentdata as $comment ) {
	$comments[] = $comment->getProperties();
      }
      $this->addOutput( 'comments', $comments );
      $result = true;
    }
    catch ( Exception $e ) {
      $this->addOutput( 'e', $e->getMessage() );
      $result = false;
    }
    return $result;
  }
  
  function makeComment ( Member $user, $locationid, $date, $message ) {
    $locationComments = new LocationComments();
    try {
      $comments = $locationComments->makeComment($user, $locationid, $date, $message);
      $result = true;
    }
    catch ( Exception $e ) {
      $this->addOutput( 'e', $e->getMessage() );
      $result = false;
    }
    return $result;
  }
  
  function deleteComment ( Member $user, $commentid ) {
    $locationComments = new LocationComments();
    try {
      $comments = $locationComments->deleteComment($user, $commentid);
      $result = true;
    }
    catch ( Exception $e ) {
      $this->addOutput( 'e', $e->getMessage() );
      $result = false;
    }
    return $result;
  }
  
  function makeReply( Member $user, $commentid, $message) {
    $locationComments = new LocationComments();
    try {
      $locationComments->makeReply($user, $commentid, $message);
      $result = true;
    }
    catch ( Exception $e ) {
      $this->addOutput( 'e', $e->getMessage() );
      $result = false;
    }
    return $result;
  }
  
  function deleteReply( $replyid ) {
    $locationComments = new LocationComments();
    try {
      $locationComments->deleteReply( $this->viewer->user, $replyid );
      $result = true;
    }
    catch ( Exception $e ) {
      $this->addOutput( 'e', $e->getMessage() );
      $result = false;
    }
    return $result;
  }
  
  function likeComment( Member $user, $commentid) {
    $locationComments = new LocationComments();
    try {
      $locationComments->likeComment($user, $commentid);
      $result = true;
    }
    catch ( Exception $e ) {
      $this->addOutput( 'e', $e->getMessage() );
      $result = false;
    }
    return $result;
  }
  
  function unlikeComment( Member $user, $commentid) {
    $locationComments = new LocationComments();
    try {
      $locationComments->unlikeComment($user, $commentid);
      $result = true;
    }
    catch ( Exception $e ) {
      $this->addOutput( 'e', $e->getMessage() );
      $result = false;
    }
    return $result;
  }
  
  function likeLocation ( $locationid ) {
    try {
      $pdo = sPDO::getInstance();
      $query = $pdo->prepare('SELECT new_location_like( :locId, :userId )');
      $query->bindParam ( ':locId', $locationid );
      $id = $this->viewer->user->userId;
      $query->bindParam ( ':userId', $id );
      $query->execute();
      $result = true;
    }
    catch ( Exception $e ) {
      $this->addOutput( 'e', $e->getMessage() );
      $result = false;
    }
    return $result;
  }
  
  function unlikeLocation ( $locationid ) {
    try {
      $pdo = sPDO::getInstance();
      $query = $pdo->prepare('SELECT del_location_like( :locId, :userId )');
      $query->bindParam ( ':locId', $locationid );
      $id = $this->viewer->user->userId;
      $query->bindParam ( ':userId', $id );
      $query->execute();
      $result = true;
    }
    catch ( Exception $e ) {
      $this->addOutput( 'e', $e->getMessage() );
      $result = false;
    }
    return $result;
  }
}
