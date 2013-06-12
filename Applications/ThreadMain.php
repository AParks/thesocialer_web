<?php

class ThreadMain extends ApplicationBase
{
  function execute( )
  {
    $this->requireAuthentication( );

    if ( (int) $this->requestValues[0] <= 0 )
    {
      $this->redirect('/messages');
    }

    $x = XSLTransformer::getInstance( );
    $node = $this->dom->createElement( 'Thread' );
    $node->appendChild($this->_getMessagesNode($this->requestValues[0]));
    $node->setAttribute('threadWith', (int) $this->requestValues[0]);
    $output = $x->transform( 'Thread', $node );
    $this->assetsManager->addCSS( 'Thread' );
    $this->assetsManager->addJavaScript( 'Thread' );
    $this->assetsManager->addJavaScript( 'MessageSender' );
    $this->display->appendOutput( $output );

    // DevelopmentFunctions::outputXML( $this->dom, $node );
  }

  private function _getMessagesNode( $otherId )
  {
    Messages::markThreadRead($this->viewer->user->userId, $otherId);
    $messages = new Thread($this->viewer->user->userId, $otherId);
    return $messages->toNode($this->dom);
  }
}
