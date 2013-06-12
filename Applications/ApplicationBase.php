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

        $config = array();
        $config['appId'] = '327877070671041';
        $config['secret'] = '86ef3bb6572ec448b644513076743896';
        $config['fileUpload'] = true; // optional

        $facebook = new Facebook($config);


// See if there is a user from a cookie
        $fb_user = $facebook->getUser();

        if ($fb_user) {
            try {
                // Proceed knowing you have a logged in user who's authenticated.
                $user_profile = $facebook->api('/me');
                $user_info = $facebook->api('/' . $fb_user);
                

                $viewerDetails['userId'] = $fb_user;
                $viewerDetails['firstName'] = $user_info['first_name'];
                $viewerDetails['lastName'] = $user_info['last_name'];
                $viewerDetails['gender'] = $user_info['gender'];
            } catch (FacebookApiException $e) {
                //              echo '<pre>' . htmlspecialchars(print_r($e, true)) . '</pre>';
                //               $user = null;
            }
        }
        $loginUrl = $facebook->getLoginUrl(array(
            'scope' => 'email,user_location,user_birthday', // Permissions to request from the user
            'redirect_uri' => 'http://thesocialer.com', // URL to redirect the user to once the login/authorization process is complete.
        ));




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
