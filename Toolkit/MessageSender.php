<?php

class MessageSender
{
  function send( Member $sender, Member $recipient, $message )
  {
    $pdo = sPDO::getInstance( );
    $query = $pdo->prepare( 'SELECT new_message( :sender_id, :recipient_id, :message )' );
    $query->bindValue( ':sender_id', $sender->userId ); 
    $query->bindValue( ':recipient_id', $recipient->userId ); 
    $query->bindValue( ':message', $message );
    return $query->execute( );
  }
}
