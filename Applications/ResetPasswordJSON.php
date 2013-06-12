<?php

class ResetPasswordJSON extends JSONApplicationBase {
  const ACTION_RESET = 'reset';
  const ACTION_REQUEST_RESET = 'req';

  public function execute( ) {
    $success = false;
    switch ( $this->requestValues[0] ) {
    case self::ACTION_RESET:
      $success = $this->resetPassword( $_POST );
      break;
    case self::ACTION_REQUEST_RESET:
      $success = $this->newResetRequest( $_POST );
      break;
    }
    $this->addOutput( 'result', $success );
    $this->output( );
    exit;
  }

  public function resetPassword( $fields ) {
    $code = $fields['ResetCode'];
    $newpass = Register::hashPassword($fields['NewPassword']);
    $pdo = sPDO::getInstance( );
    $query = $pdo->prepare( 'SELECT reset_password( :code, :newpass )' );
    $query->bindParam( ':code', $code );
    $query->bindParam( ':newpass', $newpass );
    $result = $query->execute( );
    return $result;
  }

  public function newResetRequest( $fields ) {
    $email = $fields['Email'];
    $ip = $_SERVER['REMOTE_ADDR'];
    $iv = bin2hex(mcrypt_create_iv(64, MCRYPT_DEV_URANDOM));
    $hash = hash("sha256", $iv);
    $pdo = sPDO::getInstance( );
    $query = $pdo->prepare( 'SELECT reset_password_request( :email, :ip, :code )' );
    $query->bindParam( ':email', $email );
    $query->bindParam( ':ip', $ip );
    $query->bindParam( ':code', $hash );
    $query->execute( );
    $result = $query->fetch(PDO::FETCH_ASSOC);
    $result = $result['reset_password_request'];
    
    // if the result is true, we want to send the user an email
    if ($result == true) {
      require_once "Mail.php";
      require_once "Mail/mime.php";
      
      $options['head_encoding'] = 'quoted-printable';
      $options['text_encoding'] = 'base64';
      $options['html_encoding'] = 'base64';
      $options['html_charset'] = 'UTF-8';
      $options['text_charset'] = 'gb2312';
      $options['head_charset'] = 'UTF-8';

      $to = $email;
      $subject = 'The Socialer - Reset Your Password';
      $headers['From'] = '"The Socialer" <noreply@thesocialer.com>';
      $headers['To'] = '<'.$to.'>';
      $headers['Subject'] = $subject;
      $headers['Reply-To'] = '"The Socialer" <noreply@thesocialer.com>';
      $host = "ssl://smtp.googlemail.com";
      $port = "465";
      $username = "noreply@thesocialer.com";
      $password = "aZ6eZyPob2";
      $smtp = Mail::factory( 'smtp', array ('host' => $host, 'port' => $port, 'auth' => true, 'username' => $username, 'password' => $password));
      $html = '<html>'
	.'<head>'
	.'<title>The Socialer - Password Reset Instructions</title>'
	.'</head>'
	.'<body>'
	.'We received a request to reset your password from a computer at '.$ip.'. To complete this request, just follow this link:<br />'
	.'<a href="http://www.thesocialer.com/reset?c='.$hash.'">Reset Password</a><br /><br />'
	.'If you did not request a password reset, please disregard this email.<br />'
	.'</body>'
	.'</html>';

      $text = 'We received a request to reset your password. To complete this request, just follow this link: http://www.thesocialer.com/reset?c='.$hash.' 
      If you did not request a password reset, please disregard this email.';
      
      
      $mime = new Mail_mime();
      
      $mime->setTXTBody($text);
      $mime->setHTMLBody($html);

      $message = $mime->get();
      $headers = $mime->headers($headers);      

      //send the email
      $mail = $smtp->send($to, $headers, $message);
      
      if (PEAR::isError($mail)) {
	error_log("" . $mail->getMessage() . "");
	return false;
      }
      else {
	return true;
      }
    }
    return $result;
  }
}
