<?php

class Login extends ApplicationBase {
    

    public function execute() {
        if ($this->viewer->isAuthenticated() === true) {
            $this->redirect('/trending');
        }


        $x = XSLTransformer::getInstance();
        $node = $this->dom->createElement('Login');
        $node->appendChild($this->getRegistrationYearsNode());
        $output = $x->transform('Login', $node);
        $this->display->appendOutput($output);
        $this->assetsManager->addJavaScript('Login');
        $this->assetsManager->addCSS('Login');
       // $this->assetsManager->addCSS('bootstrap');


        

        if (isset($_GET['loginFailed'])) {
            console.log("login failed");
            $this->assetsManager->addInitJavaScript('$(function( ){Header.showFailedLogin( );});');
        }
    }

    public function getRegistrationYearsNode() {
        $node = $this->dom->createElement('years');
        date_default_timezone_set("America/New_York");
        $currentYear = date('Y') - 18;

        for ($i = $currentYear; $i >= $currentYear - 85; $i--) {
            $node->appendChild($this->dom->createElement('year', $i));
        }

        return $node;
    }

}
