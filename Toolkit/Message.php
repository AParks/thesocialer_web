<?php

class Message extends ATransformableObject
{
  protected $messageId;
  protected $sender;
  protected $recipient;
  protected $message;
  protected $sentAt;
  protected $sentAtFormatted;
  protected $readAt;
  protected $preview;

  protected $publicProperties = array( 'messageId', 'sender', 'message', 'sentAt', 'readAt', 'preview', 'sentAtFormatted' );

  private function __construct( )
  {
    // Instantiate via constructById or constructByDetails
  }

  public static function constructByDetails( $messageId, $senderId, $recipientId, $message, $sentAt, $readAt )
  {
    $messageObj = new Message();
    $messageObj->messageId = $messageId;
    $messageObj->sender =  new Member($senderId);
    $messageObj->recipient = new Member($recipientId);
    $messageObj->message = nl2br( htmlentities( $message ) );
    $messageObj->sentAt = $sentAt;
    $messageObj->sentAtFormatted = $messageObj->getFormattedTimestamp($sentAt);
    $messageObj->readAt = $readAt;
    $messageObj->preview = strlen($message) > 50 ? substr($message, 0, 50) . '...' : $message;
    return $messageObj;
  }

  public function markAsRead( )
  {
    $query = sPDO::getInstance()->prepare( 'SELECT mark_read(:recipient_id, :message_id)');
    $query->bindValue(':recipient_id', $this->recipient->userId);
    $query->bindValue(':message_id', $messageId);
    return $query->execute( ); 
  }

  public function getFormattedTimestamp($timestamp)
  {
    $time = time( );

    if  ( $timestamp > $time - 60 )
    {
      return 'Just Now';
    } 

    if ( $time - $timestamp < 600 )
    {
      return floor( ( $time - $timestamp ) / 60 ) . ' minutes ago';
    }

    if ( $timestamp > mktime( 0, 0, 0 ) )
    {
      return 'Today';
    }

    if ( $timestamp > strtotime( '-1 day midnight' ) )
    {
      return 'Yesterday';
    }

    if ( date('Y') === date('Y', $timestamp) )
    {
      return date('F j', $timestamp);
    }

    return date('F j, Y', $timestamp);
  }
}
