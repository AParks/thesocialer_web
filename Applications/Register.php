<?php

class Register extends ApplicationBase {

    private $fname;
    private $email;
    private $key;

    public function execute() {

        $this->fname = $_POST['firstName'];
        $this->email = $_POST['emailAddress'];
        $key = $this->fname . $this->email . date('mY');
        $this->key = $this->hashPassword($key);

        switch ($_POST['action']) {
            case 'register':
                register();
                break;

            case 'resend_email':
                $this->sendConfirmationEmail($this->fname, $this->email, $this->key);
                break;
        }
    }

    protected function register() {
        $lname = $_POST['lastName'];
        $dob = $_POST['year'] . '-' . $_POST['month'] . '-' . $_POST['day'];
        $sex = $_POST['gender'];
        $password = $this->hashPassword($_POST['password']);


        $registration = new MemberRegistration($this->fname, $lname, $this->email, $dob, $sex, $password, NULL, 0);
        $registration->setCollege($_POST['college']);
        $registration->setLocation($_POST['location']);


        try {
            $userId = $registration->complete();
            if ($this->storeAccountConfirmationDetails($userId, $this->email))
                $this->sendConfirmationEmail();
            else
                die('Registration could not be completed.');
        } catch (PDOException $e) {
            if ($e->getCode() == '23505')
                die(' An account already exists with this email. 
                    Would you like to 
                    <a href="#myModal" role="button" class="btn" data-toggle="modal" >login </a> 
                        with this email?');
        }
    }

    function attemptLogin($email, $password) {
        $this->viewer->login($email, $password, true);
    }

    public function hashPassword($password) {
        $salt = bin2hex(mcrypt_create_iv(32, MCRYPT_DEV_URANDOM)); // get 256 random bits in hex
        $hash = hash("sha256", $salt . $password); // prepend the salt, then hash
        // store the salt and hash in the same string, so only 1 DB column is needed
        $final = $salt . $hash;
        return $final;
    }

    protected function sendConfirmationEmail() {

        $from_email = 'noreply@thesocialer.com';
        $from_password = 'noproblems';
        $subject = 'Confirm your Socialer account';
        $confirm_email = "thesocialer.com/confirm?email={$this->email}&key={$this->key}";
        $html = '<html>'
                . '<head>'
                . '<title>The Socialer - Email Confirmation</title>'
                . '</head>'
                . '<body>'
                . 'Dear ' . $this->fname . ', <br/>'
                . 'Please confirm that you signed up for the Socialer with this email address.  '
                . '<br/>'
                . "<a href=$confirm_email>Confirm</a><br/>"
                . 'TheSocialer'
                . '</body>'
                . '</html>';
        $this->email($html, $subject, $this->email, $from_email, $from_password);
    }

    protected function storeAccountConfirmationDetails($user_id) {
        $pdo = sPDO::getInstance();
        $query_delete = $pdo->prepare('DELETE from confirm where user_id = :user_id');
        $query_delete->bindValue(':user_id', $user_id);
        $query_delete->execute();

        $query = $pdo->prepare('INSERT into confirm(user_id, key, email) VALUES (:user_id, :key, :email)');
        $query->bindValue(':user_id', $user_id);
        $query->bindValue(':key', $this->key);
        $query->bindValue(':email', $this->email);
        if ($query->execute()) {
            return true;
        }
        return false;
    }

}

