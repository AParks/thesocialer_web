<?php

class LocationShare extends ATransformableObject
{
  protected $from;
  protected $to;
  protected $location;
  protected $sentAt;
  protected $date;

  protected $publicProperties = array( 'from', 'to', 'location', 'sentAt', 'date' ); 

  public function __construct( Member $from, Member $to, Location $location, $sentAt, $date )
  {
    $this->from = $from;
    $this->to = $to;
    $this->location = $location;
    $this->sentAt = $sentAt;
    $this->date = new DateObject(new DateTime($date));
  }
}
