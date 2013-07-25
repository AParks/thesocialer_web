<?php

class ChargeCard extends ApplicationBase {

    public function execute() {


        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

            // Stores errors:
            $errors = array();

            
            $amount = $_POST['checkout_total']; // in dollars
            $email = $_POST['email'];
            $userId = $_POST['userId'];
            $featured_event_id = $_POST['featured_event_id'];
            $spots = $_POST['spots'];
            error_log(print_r($_POST, true));
            $pdo = sPDO::getInstance();
                       
            if($amount == 0){
                $this->storeOrder($pdo, 'free', $userId, $featured_event_id, $spots);
                $this->updateSpots($pdo, $spots, $featured_event_id);

                exit();
            // Need a payment token:
            } else if (isset($_POST['stripeToken'])) {

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

            

            

            // Validate other form data!
            // If no errors, process the order:
            if (empty($errors)) {
                $x = XSLTransformer::getInstance();
                $this->assetsManager->addInitJavaScript("$('.NavigationLink.third').addClass('active');");
                $node = $this->dom->createElement('ChargeCard');


                // create the charge on Stripe's servers - this will charge the user's card
                try {

                    // Include the Stripe library:
                    require_once('Applications/stripe/lib/Stripe.php');

                    // set your secret key: remember to change this to your live secret key in production
                    // see your keys here https://manage.stripe.com/account
                     //  Stripe::setApiKey("sk_test_i0kS1LRFqvmuGmYLQO3VzFCH");

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
                        $this->storeOrder($pdo, $token, $userId, $featured_event_id, $spots);
                        $this->updateSpots($pdo, $spots, $featured_event_id);


                        // Send the email.
                        $this->sendReciept($amount, $email);
                        // Celebrate!
                         $paid = $this->dom->createElement('Paid');
                         $node->appendChild($paid);

                    } else { // Charge was not paid!
                        $not_paid = $this->dom->createElement('NotPaid');
                        $node->appendChild($not_paid);
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


                $output = $x->transform('ChargeCard', $node);
                $this->display->appendOutput($output);
            } // A user form submission error occurred, handled below.
        } // Form submission.
    }

    private function storeOrder($pdo, $token, $userId, $featured_event_id, $spots) {
        $query = $pdo->prepare('SELECT set_featured_event_paid_attendance_status( :featured_event_id, :user_id, :stripe_token, :spots_purchased )');
        $query->bindValue(':featured_event_id', $featured_event_id);
        $query->bindValue(':user_id', $userId);
        $query->bindValue(':stripe_token', $token);
        $query->bindValue(':spots_purchased', $spots);
      
        $result = $query->execute();
        error_log("query result" . $result);
    }
   
    private function updateSpots($pdo, $spots,$featured_event_id ){        
        $query = $pdo->prepare('UPDATE featured_events SET spots_purchased= spots_purchased + :spots where featured_event_id= :featured_event_id');
        $query->bindValue(':spots', $spots);
        $query->bindValue(':featured_event_id', $featured_event_id);

        $query->execute();
    }

    public function sendReciept($amount, $to_email) {

        $subject = 'The Socialer - Online Payment Confirmation';
        $from_email = "noreply@thesocialer.com";
        $from_password = "noproblems";

        $date = date('m/d/Y');
        $html = '<html>'
                . '<head>'
                . '<title>The Socialer - Online Payment Confirmation</title>'
                . '</head>'
                . '<body>'
                . 'Dear Customer, <br/>'
                . 'Your payment for $' . $amount . ' was processed today - ' . $date
                . '. The name on the credit card will on a list at the door. <br/>See you there! <br/>'
                . 'The Socialer'
                . '</body>'
                . '</html>';


        $this->email($html, $subject, $to_email, $from_email, $from_password);
    }

}

?>
