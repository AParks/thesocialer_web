<?php

class ChargeCard extends ApplicationBase {

    public function execute() {


        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

            // Stores errors:
            $errors = array();

            // Need a payment token:
            if (isset($_POST['stripeToken'])) {

                $token = $_POST['stripeToken'];

                // Check for a duplicate submission, just in case:
                // Uses sessions, you could use a cookie instead.
                if (isset($_SESSION['token']) && ($_SESSION['token'] == $token)) {
                    $errors['token'] = 'You have apparently resubmitted the form. Please do not do that.';
                } else { // New submission.
                    $_SESSION['token'] = $token;
                }
            } else {
                $errors['token'] = 'The order cannot be processed. Please make sure you have JavaScript enabled and try again.';
            }

            // Set the order amount somehow:
            $amount = $_POST['checkout_total']; // in dollars
            $email = $_POST['email'];
            $userId = $_POST['userId'];
            $featured_event_id = $_POST['featured_event_id'];

            // Validate other form data!
            // If no errors, process the order:
            if (empty($errors)) {

                // create the charge on Stripe's servers - this will charge the user's card
                try {

                    // Include the Stripe library:
                    require_once('Applications/stripe/lib/Stripe.php');

                    // set your secret key: remember to change this to your live secret key in production
                    // see your keys here https://manage.stripe.com/account
                 //   Stripe::setApiKey("sk_test_i0kS1LRFqvmuGmYLQO3VzFCH");
                 
                    Stripe::setApiKey("sk_live_oZAmTuyJwbxUWN0DYMoCEQgT");

                    // Charge the order:

                    $charge = Stripe_Charge::create(array(
                                "amount" => $amount * 100, // amount in cents, again
                                "currency" => "usd",
                                "card" => $token,
                                "description" => 'TheSocialer.com'
                                    )
                    );

                    // Check that it was paid:
                    if ($charge->paid == true) {

                        // Store the order in the database.
                        $this->storeOrder($token, $userId, $featured_event_id);


                        // Send the email.
                        $this->sendConfirmationEmail($amount, $email);
                        // Celebrate!
                        $x = XSLTransformer::getInstance();
                        $node = $this->dom->createElement('ChargeCard');
                        $this->assetsManager->addInitJavaScript("$('.NavigationLink.third').addClass('active');");
                        $output = $x->transform('ChargeCard', $node);
                        $this->display->appendOutput($output);
                    } else { // Charge was not paid!	
                        echo '<div class="alert alert-error"><h4>Payment System Error!</h4>Your payment could NOT be processed (i.e., you have not been charged) because the payment system rejected the transaction. You can try again or use another card.</div>';
                    }
                } catch (Stripe_CardError $e) {
                    // Card was declined.
                    $e_json = $e->getJsonBody();
                    $err = $e_json['error'];
                    $errors['stripe'] = $err['message'];
                } catch (Stripe_ApiConnectionError $e) {
                    // Network problem, perhaps try again.
                } catch (Stripe_InvalidRequestError $e) {
                    // You screwed up in your programming. Shouldn't happen!
                } catch (Stripe_ApiError $e) {
                    // Stripe's servers are down!
                } catch (Stripe_CardError $e) {
                    // Something else that's not the customer's fault.
                }
            } // A user form submission error occurred, handled below.
        } // Form submission.
    }

    public function storeOrder($token, $userId, $featured_event_id) {
        $pdo = sPDO::getInstance();
        $query = $pdo->prepare('SELECT set_featured_event_paid_attendance_status( :featured_event_id, :user_id, :stripe_token )');
        $query->bindValue(':featured_event_id', $featured_event_id);
        $query->bindValue(':user_id', $userId);
        $query->bindValue(':stripe_token', $token);
        $result = $query->execute();
        error_log("query result" . $result);
    }

    public function sendConfirmationEmail($amount, $to_email) {

        require_once "Mail.php";
        require_once "Mail/mime.php";

        $options['head_encoding'] = 'quoted-printable';
        $options['text_encoding'] = 'base64';
        $options['html_encoding'] = 'base64';
        $options['html_charset'] = 'UTF-8';
        $options['text_charset'] = 'gb2312';
        $options['head_charset'] = 'UTF-8';

        $subject = 'The Socialer - Online Payment Confirmation';
        $headers['From'] = '"The Socialer" <noreply@thesocialer.com>';
        $headers['To'] = '<' . $to_email . '>';
        $headers['Subject'] = $subject;
        $headers['Reply-To'] = '"The Socialer" <noreply@thesocialer.com>';
        $host = "ssl://smtp.googlemail.com";
        $port = "465";
        $username = "noreply@thesocialer.com";
        $password = "aZ6eZyPob2";
        $smtp = Mail::factory('smtp', array('host' => $host, 'port' => $port, 'auth' => true, 'username' => $username, 'password' => $password));
        $date = date('m/d/Y');
        $html = '<html>'
                . '<head>'
                . '<title>The Socialer - Online Payment Confirmation</title>'
                . '</head>'
                . '<body>'
                . 'Dear Customer, <br/>'
                . 'Your payment for $' . $amount . ' was processed today - ' . $date
                . '<br/>See you there! <br/>'
                . 'TheSocialer'
                . '</body>'
                . '</html>';



        $mime = new Mail_mime();

        $mime->setHTMLBody($html);

        $message = $mime->get();
        $headers = $mime->headers($headers);

        //send the email
        $mail = $smtp->send($to_email, $headers, $message);

        if (PEAR::isError($mail)) {
            error_log("" . $mail->getMessage() . "");
        }
    }

}

?>
