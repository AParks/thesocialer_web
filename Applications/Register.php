<?php

class Register extends ApplicationBase {

    const URL_SUCCESSFUL_LOGIN = '/trending';
    const URL_SUCCESSFUL_REGISTRATION = '/confirm';

    public function execute() {
        // the pass may already have been hashed, but we do it again to add some salt

        $fname = $_POST['firstName'];
        $lname = $_POST['lastName'];
        $email = $_POST['emailAddress'];
        $dob = $_POST['year'] . '-' . $_POST['month'] . '-' . $_POST['day'];
        $sex = $_POST['gender'];
        $password = $this->hashPassword($_POST['password']);


        $registration = new MemberRegistration($fname, $lname, $email, $dob, $sex, $password, NULL, 0);
        $registration->setCollege($_POST['college']);
        $registration->setLocation($_POST['location']);


        try {
            $userId = $registration->complete();

            $key = $fname . $email . date('mY');
            $key = $this->hashPassword($key);
            $this->sendConfirmationEmail($fname, $email, $key);
            $this->storeAccountConfirmationDetails($userId, $key, $email);
        } catch (PDOException $e) {
            if ($e->getCode() == '23505')
                die(' An account already exists with this email. 
                    Would you like to 
                    <a href="#myModal" role="button" class="btn" data-toggle="modal" >login </a> 
                        with this email?');
        }
    }

    function attemptLogin($email, $password) {
        error_log('email' . $email);
        $this->viewer->login($email, $password, true);
    }

    public function hashPassword($password) {
        $salt = bin2hex(mcrypt_create_iv(32, MCRYPT_DEV_URANDOM)); // get 256 random bits in hex
        $hash = hash("sha256", $salt . $password); // prepend the salt, then hash
        // store the salt and hash in the same string, so only 1 DB column is needed
        $final = $salt . $hash;
        return $final;
    }

    protected function sendConfirmationEmail($first_name, $to_email, $key) {

        require_once "Mail.php";
        require_once "Mail/mime.php";

        $options['head_encoding'] = 'quoted-printable';
        $options['text_encoding'] = 'base64';
        $options['html_encoding'] = 'base64';
        $options['html_charset'] = 'UTF-8';
        $options['text_charset'] = 'gb2312';
        $options['head_charset'] = 'UTF-8';

        $subject = 'Confirm your Socialer account';
        $headers['From'] = '"The Socialer" <noreply@thesocialer.com>';
        $headers['To'] = '<' . $to_email . '>';
        $headers['Subject'] = $subject;
        $headers['Reply-To'] = '"The Socialer" <noreply@thesocialer.com>';
        $host = "ssl://smtp.googlemail.com";
        $port = "465";
        $username = "noreply@thesocialer.com";
        $password = "aZ6eZyPob2";
        $smtp = Mail::factory('smtp', array('host' => $host, 'port' => $port, 'auth' => true, 'username' => $username, 'password' => $password));

        $confirm_email = "thesocialer.com/confirm?email={$to_email}&key={$key}";
        $html = '<html>'
                . '<head>'
                . '<title>The Socialer - Email Confirmation</title>'
                . '</head>'
                . '<body>'
                . 'Dear ' . $first_name . ', <br/>'
                . 'Please confirm that you signed up for the Socialer with this email address.  '
                . '<br/>'
                . "<a href=$confirm_email>Confirm</a><br/>"
                . 'TheSocialer'
                . '</body>'
                . '</html>';



        $mime = new Mail_mime();

        $mime->setHTMLBody($html);

        $message = $mime->get();
        $headers = $mime->headers($headers);

        //send the email
        $mail = $smtp->send($to_email, $headers, $message);
        error_log('mail sent?');
        if (PEAR::isError($mail)) {
            error_log("error sending mail " . $mail->getMessage() . "");
        }
    }

    protected function storeAccountConfirmationDetails($user_id, $key, $email) {
        $pdo = sPDO::getInstance();
        $query = $pdo->prepare('INSERT into confirm(user_id, key, email) VALUES (:user_id, :key, :email)');
        $query->bindValue(':user_id', $user_id);
        $query->bindValue(':key', $key);
        $query->bindValue(':email', $email);
        $query->execute();
    }

}

