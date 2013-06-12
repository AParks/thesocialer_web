<?php

class MessagesMain extends ApplicationBase
{
  function execute( )
  {
    $this->requireAuthentication( );

    $x = XSLTransformer::getInstance( );
    $node = $this->dom->createElement( 'Messages' );
    $node->appendChild($this->_getMessagesNode());
    $output = $x->transform( 'Messages', $node );
    $this->assetsManager->addCSS( 'Messages' );
    $this->assetsManager->addJavaScript( 'Messages' );
    $this->display->appendOutput( $output );

    // DevelopmentFunctions::outputXML( $this->dom, $node );
  }

  private function _getMessagesNode( )
  {
    $messages = new Messages($this->viewer->user->userId);
    return $messages->toNode($this->dom);
  }
}
