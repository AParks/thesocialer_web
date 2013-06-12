<?php

class Thread extends ATransformableObject
{
  protected $publicProperties = array( 'messages' );
  protected $messages = array( );
  protected $recipientId;
  protected $senderId;

  public function __construct( $memberId1, $memberId2) 
  {
    $this->memberOne = $memberId1;
    $this->memberTwo = $memberId2;;
    $this->_load();
  }

  private function _load()
  {
    $query = sPDO::getInstance()->prepare( 'SELECT message_id, sender_id, recipient_id, EXTRACT(EPOCH FROM sent_at) AS sent_at, EXTRACT(EPOCH FROM read_at) AS read_at, message FROM thread(:member_id_1, :member_id_2)');
    $query->bindValue(':member_id_1', $this->memberOne);
    $query->bindValue(':member_id_2', $this->memberTwo);
    $query->execute();
    $messages = $query->fetchAll(PDO::FETCH_OBJ);

    foreach ( $messages as $message )
    {
      $this->messages[] = Message::constructByDetails( $message->message_id, $message->sender_id, $message->recipient_id, $message->message, $message->sent_at, $message->read_at );
    }
  }
}
