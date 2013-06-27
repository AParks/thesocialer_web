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
        $this->assetsManager->addJavaScript('uservoice');


        $this->assetsManager->addCSS('Main');
        $this->assetsManager->addCSS('Header');
        $this->assetsManager->addCSS('SocialInboxManager');
        #   $this->assetsManager->addCSS( 'bootstrap' );


        $this->display = DisplayManager::getInstance();
        $this->viewer = Viewer::getInstance();





        $viewerDetails = array();


        if ($this->viewer->user != null) {
            $viewerDetails['userId'] = $this->viewer->user->userId;
             if ($this->viewer->user->photo->path == 'facebook'){
                $viewerDetails['photo'] = 'https://graph.facebook.com/' . $this->viewer->user->fb_id . '/picture?type=square';
             }
            else
                $viewerDetails['photo'] = $this->viewer->user->photo->paths[Photo::SIZE_SMALL];
            $viewerDetails['firstName'] = $this->viewer->user->firstName;
            $viewerDetails['lastName'] = $this->viewer->user->lastName;
            $viewerDetails['gender'] = $this->viewer->user->gender;
            $viewerDetails['tags'] = $this->viewer->user->tags;
        }

        $this->assetsManager->addInitJavaScript('var Viewer = ' . json_encode($viewerDetails) . ';');
        
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

    protected function email($html , $subject, $to_email, $from_email, $from_password) {
        require_once "Mail.php";
        require_once "Mail/mime.php";

        $options['head_encoding'] = 'quoted-printable';
        $options['text_encoding'] = 'base64';
        $options['html_encoding'] = 'base64';
        $options['html_charset'] = 'UTF-8';
        $options['text_charset'] = 'gb2312';
        $options['head_charset'] = 'UTF-8';

        $headers['From'] = '"The Socialer" <'.$from_email.'>';
        $headers['To'] = '<' . $to_email . '>';
        $headers['Subject'] = $subject;
        $headers['Reply-To'] = '"The Socialer" <.'.$from_email.'>';
        $host = "ssl://smtp.googlemail.com";
        $port = "465";
        $smtp = Mail::factory('smtp', array('host' => $host, 'port' => $port, 'auth' => true, 'username' => $from_email, 'password' => $from_password));
    
        
        $mime = new Mail_mime();

        $mime->setHTMLBody($html);

        $message = $mime->get();
        $headers = $mime->headers($headers);

        //send the email
        $mail = $smtp->send($to_email, $headers, $message);
        if (PEAR::isError($mail)) {
            error_log("error sending mail " . $mail->getMessage() . "");
            return false;
        }
        return true;
    }
 

}
