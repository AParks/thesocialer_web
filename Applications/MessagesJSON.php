<?php

class MessagesJSON extends JSONApplicationBase
{
  const ACTION_SEND = 'send';

  function execute( )
  {
    switch ( $this->requestValues[0] )
    {
    case self::ACTION_SEND:
      $result = $this->send(Viewer::getInstance( )->user, new Member($_GET['to']), $_GET['message']);
      break;
    }

    echo json_encode( array( 'success' => $result ) );

    exit;
  }

  private function send(Member $sender, Member $recipient, $message)
  {
    $messageSender = new MessageSender();
    return $messageSender->send($sender, $recipient, $message);
  }
}
