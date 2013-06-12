<?php

class FriendRequest extends ATransformableObject
{
  protected $from;
  protected $to;
  protected $sentAt;

  protected $publicProperties = array( 'from', 'to', 'sentAt' ); 

  public function __construct( Member $from, Member $to, $sentAt )
  {
    $this->from = $from;
    $this->to = $to;
    $this->sentAt = $sentAt;
  }
}
