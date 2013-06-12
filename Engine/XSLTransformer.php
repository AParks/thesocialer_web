<?php

class XSLTransformer {
  protected $processor;

  protected function __construct( ) {
    $this->processor = new XSLTProcessor();  
  }

  public static function getInstance( ) {
    static $instance;

    if ( $instance === null ) {
      $instance = new XSLTransformer();
    }

    return $instance;
  }

  public function transform( $styleSheet, $domNode ) {
    $xsl = new DomDocument;
    $xsl->load( AutoLoader::getInstance( )->getFile( 'XSL', $styleSheet ) );

    $this->processor->importStyleSheet( $xsl );

    $xml = new DomDocument;
    $xml->loadXML( $xml->saveXML( $xml->importNode( $domNode, true ) ) );

    //$return = $this->processor->transformToXML( $xml );
    
    $return = $this->processor->transformToDoc( $xml );

    
    $socNodes = $return->getElementsByTagNameNS('http://kemmerer.co', '*');

    // Converting the DOMNodeList to an array seems to be necessary.
    // Without it, the DOMNodeList was emptying itself after the replaceChild
    // occurs in the document.
    $nodesToReplace = array( );
    foreach ( $socNodes as $s ) {
      $nodesToReplace[] = $s;
    }

    foreach ( $nodesToReplace as $socNode ) {
      $className = 'to' . ucwords(substr( $socNode->tagName, 4 )); 

      if ( class_exists($className) === false ) {
        // @todo THrow exception?  Does anything show up in markup?
        continue;
      }

      $attributes = array( );
      foreach ( $socNode->attributes as $attribute ) {
        $attributes[$attribute->name] = $attribute->value;
      }

      $object = new $className( $attributes, $socNode->nodeValue );

      $parent = $socNode->parentNode;

      $replacementNode = $return->createCDATASection( $object->getMarkup( ) );

      $parent->replaceChild( $replacementNode, $socNode );
    }

    $return= $return->saveHTML();
    return $return;
  }
}
