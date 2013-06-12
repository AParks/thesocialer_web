<?php

class LocationComment extends ATransformableObject
{
  protected $id;
  protected $user;
  protected $locationid;
  protected $locationname;
  protected $locationdate;
  protected $posted_at;
  protected $message;
  protected $likes = array();
  protected $replies = array();

  protected $publicProperties = array( 'id', 'user', 'locationid', 'locationname', 'locationdate', 'posted_at', 'message', 'likes', 'replies' ); 

  public function __construct( $commentid, Member $user, $locationid, $locationname, $date, $posted_at, $message )
  {
  	$this->id = $commentid;
    $this->user = $user->getProperties();
    $this->locationid = $locationid;
    $this->locationname = $locationname;
    $this->locationdate = $date;
    $this->posted_at = $posted_at;
    $this->message = $message;
    
    $this->getLikes();
    
    $repliesquery = sPDO::getInstance( )->prepare( 'SELECT u.first_name, lcr.reply_id, lcr.user_id, lcr.posted_at, lcr.message FROM location_comments lc, location_comment_replies lcr, users u WHERE lc.comment_id = lcr.comment_id AND lc.comment_id = :comment_id AND lcr.user_id = u.user_id ORDER BY lcr.posted_at ASC');
    $repliesquery->bindValue(':comment_id', $this->id );
    $repliesquery->execute();
    
    foreach ( $repliesquery->fetchAll(PDO::FETCH_ASSOC) as $reply ) {
    	$this->replies[] = $reply;
    }
  }
  
  public function getLikes( ) {
  	$likequery = sPDO::getInstance( )->prepare( 'SELECT user_id FROM location_comment_likes WHERE comment_id = :comment_id');
  	$likequery->bindValue(':comment_id', $this->id );
  	$likequery->execute();
  	foreach ( $likequery->fetchAll(PDO::FETCH_ASSOC) as $like ) {
  		$this->likes[] = $like['user_id'];
  	}
  }
}
