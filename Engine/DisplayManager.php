<?php

class DisplayManager {

    protected $output = '';

    protected function __construct() {
        
    }

    public function getInstance() {
        static $instance;

        if ($instance === null) {
            $instance = new DisplayManager( );
        }

        return $instance;
    }

    protected function getHeader() {
        
    }

    public function appendOutput($output) {
        $this->output .= $output;
    }

    //in the registration form
    //add birth years, with the
    //earliest being for someone who is 18 years old
    public function getRegistrationYearsNode($dom) {
        $node = $dom->createElement('years');
        date_default_timezone_set("America/New_York");
        $currentYear = date('Y') - 18;

        for ($i = $currentYear; $i >= $currentYear - 85; $i--) {
            $node->appendChild($dom->createElement('year', $i));
        }

        return $node;
    }

    public function output() {
        $dom = new DomDocument( );
        $node = $dom->createElement('Header');

        $viewer = Viewer::getInstance();
        $node->appendChild($viewer->toNode($dom));
        $node->appendChild($this->getRegistrationYearsNode($dom)); 

        if ($viewer->isAuthenticated()) {
            $node->setAttribute('unreadCount', Messages::getUnreadCount($viewer->user->userId));
            $socialInbox = new SocialInboxManager( );
            $node->appendChild($socialInbox->get($viewer->user, $dom));
        }

        $jsNode = $dom->createElement('JavaScript');
        $node->appendChild($jsNode);

        $assetsManager = AssetsManager::getInstance();

        foreach ($assetsManager->getJavaScript() as $js) {
            $jsNode->appendChild($dom->createElement('file', $js));
        }

        $initJSNode = $dom->createElement('initJavaScript', implode('', $assetsManager->getInitJavaScript()));
        $node->appendChild($initJSNode);

        $cssNode = $dom->createElement('CSS');
        $node->appendChild($cssNode);
        foreach ($assetsManager->getCSS() as $css) {
            $cssNode->appendChild($dom->createElement('file', $css));
        }

        $markup = $dom->createElement('markup');
        $markup->appendChild($dom->createCDATASection($this->output));
        $node->appendChild($markup);

        $meebo = new Meebo( );
        $node->appendChild($meebo->toNode($dom));

        // DevelopmentFunctions::outputXML( $dom, $node );

        $return = XSLTransformer::getInstance()->transform('Header', $node);
        echo $return;
    }

}
