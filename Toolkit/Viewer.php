<?php

class Viewer extends ATransformableObject {

    const ID_NOT_LOGGED_IN = -1;

    protected $userId = self::ID_NOT_LOGGED_IN;
    protected $user;
    protected $publicProperties = array('userId', 'user');

    protected function __construct() {


        $config = array();
        $config['appId'] = '327877070671041';
        $config['secret'] = '86ef3bb6572ec448b644513076743896';
        $config['fileUpload'] = true; // optional

        $facebook = new Facebook($config);


        // See if there is a user from a cookie
        $fb_user = $facebook->getUser();
        $access_token = $facebook->getAccessToken();
        $loginUrl = $facebook->getLoginUrl(array(
            'scope' => 'email,user_location,user_birthday', // Permissions to request from the user
            'redirect_uri' => 'http://thesocialer.com', // URL to redirect the user to once the login/authorization process is complete.
        ));


        /* if ($fb_user) {
          try {

          // Proceed knowing you have a logged in user who's authenticated.
          $user_profile = $facebook->api('/me');
          $user_info = $facebook->api('/' . $fb_user);
          $_SESSION['fb_access_token'] = $access_token;

          //  $this->setUserId($fb_user);
          $this->fb_login($user_info, $fb_user);
          } catch (FacebookApiException $e) {
          error_log($e);
          }
          }
         */
        if (isset($_COOKIE['userlogin'])) {  //set user id from cookie
            $this->setUserId($_COOKIE['userlogin']);
        }

        if (!isset($_SESSION['userId'])) {
            @session_start();
        } else {
            $this->setUserId((int) $_SESSION['userId']);
        }
    }

    protected function setUserId($userId) {
        $userId = (int) $userId;
        $this->userId = $userId;
        $_SESSION['userId'] = $this->userId;
        try {
            $this->user = new Member($this->userId);
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }

    public function __destruct() {
        $_SESSION['userId'] = $this->userId;
        session_write_close();
    }

    public function getInstance() {
        static $instance;
        if ($instance === null) {
            $instance = new Viewer( );
        }

        return $instance;
    }

    public function isAuthenticated() {
        return $this->userId !== self::ID_NOT_LOGGED_IN;
    }

    public function fb_login($user_info, $fb_id) {

        $pdo = sPDO::getInstance();
        $query = $pdo->prepare('SELECT user_id FROM users WHERE fb_id = :fb_id');
        $query->bindParam(':fb_id', $fb_id);
        $query->execute();
        $row = $query->fetch(PDO::FETCH_ASSOC);
        $user_id = $row['user_id'];
        if (!$user_id) { //if the user has not yet connected to fb
            $query_email = $pdo->prepare('SELECT user_id FROM users WHERE email_address = :email');
            $query_email->bindParam(':email', $user_info['email']);
            $query_email->execute();
            $row_email = $query_email->fetch(PDO::FETCH_ASSOC);
            $user_id = $row_email['user_id'];
            if ($user_id) {
                //if fb email matches an email in the socialer db, merge the account info
                $query_update = $pdo->prepare('UPDATE users SET fb_id = :fb_id WHERE user_id = :user_id');
                $query_update->bindParam(':user_id', $user_id);
                $query_update->bindParam(':fb_id', $fb_id);
                if ($query_update->execute()) {
                    echo "hello";
                }
            } else { //create a new user
                $dob_arry = explode("/", $user_info['birthday']);
                $month = $dob_arry[0];
                $day = $dob_arry[1];
                $year = $dob_arry[2];
                $college = NULL;
                foreach ($user_info['education'] as $education) {
                    if ($education['type'] == 'College') {
                        $college = $education['school']['name'];
                        break;
                    }
                }

                $registration = new MemberRegistration( );
                $registration->setDetails($user_info['first_name'], $user_info['last_name'], $user_info['email'], $year . '-' . $month . '-' . $day, $user_info['gender'], NULL);
                $registration->setCollege($college);
                $registration->setLocation($user_info['location']['name']);
                $user_id = $registration->complete();
            }
        }
        $this->setCookie($user_id);
        $this->setUserId($user_id);

    }

    public function logout() {
        setcookie('userlogin', NULL, time() - 86400, '/', 'thesocialer.com');
        setcookie(session_name(), '', time() - 86400, '/');
        error_log("logging out yo");
        session_destroy();
    }

    public function login($email, $password, $remember) {

        $pdo = sPDO::getInstance();
        $query = $pdo->prepare('SELECT user_id, password FROM users WHERE email_address = :email');
        $query->bindParam(':email', $email);
        $query->execute();
        $row = $query->fetch(PDO::FETCH_ASSOC);
        if ($storedhash = $row['password']) {
            if ($this->validatePassword($password, $storedhash)) {
                $userId = $row['user_id'];
                $this->setUserId((int) $userId);
                $query = $pdo->prepare('SELECT login( :user_id, :ip_address )');
                $query->bindValue(':user_id', $userId);
                $query->bindValue(':ip_address', $_SERVER['REMOTE_ADDR']);
                $query->execute();

                error_log(" REMEMBER" . $remember);
                //remember user login even after the browser window is closed
                if ($remember)
                    $this->setCookie($userId);
                    
                
                return $userId;
            }
        }

        throw new Exception('Invalid user credentials.');
    }

    protected function setCookie($userId) {

        $number_of_days = 30;
        $date_of_expiry = time() + 60 * 60 * 24 * $number_of_days;
        setcookie("userlogin", $userId, $date_of_expiry, "/", "thesocialer.com");
    }

    public function validatePassword($password, $correctHash) {
        $salt = substr($correctHash, 0, 64); //get the salt from the front of the hash
        $validHash = substr($correctHash, 64, 64); //the SHA256
        $testHash = hash("sha256", $salt . $password); //hash the password being tested
        return $testHash === $validHash;
    }

}
