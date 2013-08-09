<?php

require_once 'mixpanel-php/lib/Mixpanel.php';

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
        $query = $pdo->prepare('SELECT user_id, fb_id FROM users WHERE email_address = :email OR fb_id= :fb_id');
        $query->bindParam(':email', $user_info['email']);
        $query->bindParam(':fb_id', $fb_id);

        $query->execute();
        $row = $query->fetch(PDO::FETCH_ASSOC);
        $user_id = $row['user_id'];
        if ($user_id) {
            if (!$row['fb_id'])
                $this->addFaceBookId($pdo, $user_id, $fb_id);

            
            $this->setCookie($user_id);
            $this->setUserId($user_id);
            
            
            if($this->aboutMeEmpty($pdo, $user_id)){
                $about = array("AboutMe" => $user_info['bio']);
                $this->user->update($about);
            }
        }
        else {
            $user_id = $this->createNewUser($user_info, $fb_id);

            $this->setCookie($user_id);
            $this->setUserId($user_id);
        }

        Photo::create($this->user, 'facebook');
    }
    
    private function aboutMeEmpty($pdo, $user_id){
        $query = $pdo->prepare('SELECT profile_field_value FROM profile_field_values WHERE profile_field_id = 3 AND user_id = :user_id');
        $query->bindParam(':user_id', $user_id);
        $query->execute();
        $row = $query->fetch(PDO::FETCH_ASSOC);
        $bio = $row['profile_field_value'];
        return (!bio || $bio == '') ;
    }

    private function createNewUser($user_info, $fb_id) {
        $fname = $user_info['first_name'];
        $lname = $user_info['last_name'];
        $email = $user_info['email'];
        $bio = $user_info['bio'];
        $dob = $this->formatDOB($user_info['birthday']);
        $college = $this->getCollege($user_info['education']);
        $gender = ($user_info['gender'] == "female" ? 'f' : 'm');
        $registration = new MemberRegistration($fname, $lname, $email, $dob, $gender, NULL, $fb_id, 1);
        $registration->setCollege($college);
        $registration->setBio($bio);
        $registration->setLocation($user_info['location']['name']);
        $user_id = $registration->complete();
        return $user_id;
    }

    private function addFaceBookId($pdo, $user_id, $fb_id) {
        $query_update = $pdo->prepare('UPDATE users SET fb_id = :fb_id WHERE user_id = :user_id');
        $query_update->bindParam(':user_id', $user_id);
        $query_update->bindParam(':fb_id', $fb_id);
        if ($query_update->execute()) {
            error_log('account merge successful');
        }
    }

    private function formatDOB($birthday) {
        $dob_arry = explode("/", $birthday);
        $month = $dob_arry[0];
        $day = $dob_arry[1];
        $year = $dob_arry[2];
        $dob = $year . '-' . $month . '-' . $day;
        return $dob;
    }

    //facebook provides an array of schools attended. 
    // We grab the first school that is a college
    private function getCollege($schools) {
        $college = NULL;
        foreach ($schools as $school) {
            if ($school['type'] == 'College') {
                $college = $school['school']['name'];
                break;
            }
        }
        return $college;
    }

    public function logout() {
        setcookie('userlogin', NULL, time() - 86400, '/', 'thesocialer.com');
        setcookie(session_name(), '', time() - 86400, '/');
        session_destroy();
    }

    public function login($email, $password, $remember) {

        $pdo = sPDO::getInstance();
        $query = $pdo->prepare('SELECT user_id, password, first_name, active FROM users WHERE email_address = :email');
        $query->bindParam(':email', $email);
        $query->execute();
        $row = $query->fetch(PDO::FETCH_ASSOC);


        if ($row) {
            $userId = $row['user_id'];
            $storedhash = $row['password'];
            if ($row['active'] == 1) {
                if ($this->validatePassword($password, $storedhash)) {
                    $this->setUserId((int) $userId);
                    $query = $pdo->prepare('SELECT login( :user_id, :ip_address )');
                    $query->bindValue(':user_id', $userId);
                    $query->bindValue(':ip_address', $_SERVER['REMOTE_ADDR']);
                    $query->execute();

                    //remember user login even after the browser window is closed
                    if ($remember)
                        $this->setCookie($userId);


                    return $userId;
                }
                else
                    throw new Exception(" Sorry, that password isn't right. We can help you <a class='ForgotLink'>recover your password</a>.");
                    
            }else if ($row['active'] == 0) {
                $name = $row['first_name'];
                throw new Exception(" An account with that email has already 
                    been registered, but was never activated. 
                    <a id='confirm' user_id=$userId email='$email' firstName='$name'>Resend confirmation email</a>.");
            }
        }
        throw new Exception(' Sorry, we could not find an account with that email.');
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
