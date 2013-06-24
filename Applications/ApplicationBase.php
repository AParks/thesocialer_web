<?php

require_once("php-sdk/facebook.php");

abstract class ApplicationBase implements IApplication {

    protected $assetsManager;
    protected $display;
    protected $dom;
    protected $requestValues;
    protected $viewer;

    public function __construct() {
        $this->dom = new DomDocument;
        $this->assetsManager = AssetsManager::getInstance();
        $this->assetsManager->addJavaScript('Header');
        $this->assetsManager->addJavaScript('sha256');
        $this->assetsManager->addJavaScript('jquery.validate');
        $this->assetsManager->addJavaScript('Main');
        $this->assetsManager->addJavaScript('SocialInboxManager');
        $this->assetsManager->addJavaScript('bootstrap');


        $this->assetsManager->addCSS('Main');
        $this->assetsManager->addCSS('Header');
        $this->assetsManager->addCSS('SocialInboxManager');
        #   $this->assetsManager->addCSS( 'bootstrap' );


        $this->display = DisplayManager::getInstance();
        $this->viewer = Viewer::getInstance();





        $viewerDetails = array();


        if ($this->viewer->user != null) {
            $viewerDetails['userId'] = $this->viewer->user->userId;
            $viewerDetails['photo'] = $this->viewer->user->photo->paths[Photo::SIZE_SMALL];
            $viewerDetails['firstName'] = $this->viewer->user->firstName;
            $viewerDetails['lastName'] = $this->viewer->user->lastName;
            $viewerDetails['gender'] = $this->viewer->user->gender;
            $viewerDetails['tags'] = $this->viewer->user->tags;
        }

        $this->assetsManager->addInitJavaScript('var Viewer = ' . json_encode($viewerDetails) . ';');
        //      $this->assetsManager->addInitJavaScript("var login = '". $loginUrl . "';" );
//echo $loginUrl;
    }

    public function setRequestValues($values) {
        if (gettype($values) !== 'array') {
            throw new InvalidArgumentException(sprintf('%s::%s', __CLASS__, __FUNCTION__));
        }

        $this->requestValues = $values;
    }

    protected function getLoggedInMemberNode() {
        return $this->viewer->toNode($this->dom);
    }

    protected function redirect($url) {
        header('Location: ' . $url);
        exit;
    }

    public function requireAuthentication() {
        if ($this->viewer->isAuthenticated() === false) {
            $this->redirect('/');
        }
    }

 

}
