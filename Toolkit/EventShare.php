<?php

class EventShare extends ATransformableObject
{
  protected $from;
  protected $to;
  protected $event;
  protected $sentAt;

  protected $publicProperties = array( 'from', 'to', 'event', 'sentAt' ); 

  public function __construct( Member $from, Member $to, $event, $sentAt )
  {
    $this->from = $from;
    $this->to = $to;
    $this->event = $event;
    $this->sentAt = $sentAt;
  }
}
