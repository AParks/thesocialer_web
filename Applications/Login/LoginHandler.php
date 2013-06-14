<?php

class LoginHandler extends JSONApplicationBase {    

    const ACTION_FB_LOGIN = 'fb';
    const ACTION_LOGIN = 'normal';

    function execute() {
        try {
            switch ($this->requestValues[0]) {
                case self::ACTION_LOGIN:
                    $this->attemptLogin($_POST['LoginEmail'], $_POST['LoginPassword'], $_POST['RememberMe']);
                    break;
                case self::ACTION_FB_LOGIN:
                    $this->viewer->fb_login($_POST['user_info'], $_POST['fb_id']);
                    break;
                
            }
        } catch (Exception $e) {
            error_log($e->getFile() . $e->getLine() . $e->getMessage());
            die('Invalid Login');
        }
        exit;
    }

    function attemptLogin($email, $password, $remmber) {
        $this->viewer->login($email, $password, $remmber);
    }

}
