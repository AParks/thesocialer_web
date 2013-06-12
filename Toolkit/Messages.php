<?php

class Messages extends ATransformableObject {
  protected $publicProperties = array( 'messages' );
  protected $messages = array( );
  protected $recipientId;

  public function __construct( $recipientId ) {
    $this->recipientId = $recipientId;
    $this->_load();
  }

  private function _load() {
    $querytext = 'SELECT message_id, sender_id, EXTRACT(EPOCH FROM sent_at) AS sent_at, EXTRACT(EPOCH FROM read_at) AS read_at, message '
      .'FROM get_messages(:recipient_id) '
      .'';
    $query = sPDO::getInstance()->prepare( $querytext );
    $query->bindValue(':recipient_id', $this->recipientId);
    $query->execute();
    $messages = $query->fetchAll(PDO::FETCH_OBJ);

    foreach ( $messages as $message ) {
      $this->messages[$message->sender_id] = Message::constructByDetails( $message->message_id, $message->sender_id, $this->recipientId, $message->message, $message->sent_at, $message->read_at );
    }
  }

  public static function getUnreadCount($userId) {
    $query = sPDO::getInstance()->prepare('SELECT unread_count(:user_id)');
    $query->bindValue(':user_id', $userId);
    $query->execute();
    return $query->fetchColumn(0);
  }

  public static function markThreadRead($recipientId, $senderId) {
    $query = sPDO::getInstance()->prepare('SELECT mark_thread_read(:recipient_id, :sender_id)');
    $query->bindValue(':recipient_id', $recipientId);
    $query->bindValue(':sender_id', $senderId);
    $query->execute();
  }
}
