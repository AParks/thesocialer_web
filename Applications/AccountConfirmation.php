<?php

class AccountConfirmation extends ApplicationBase {

    public function execute() {
        $x = XSLTransformer::getInstance();
        $node = $this->dom->createElement('AccountConfirmation');

        $key = $_GET['key'];
        $email = $_GET['email'];
        if (isset($email) && isset($key)) {
            $pdo = sPDO::getInstance();

            if ($this->validConfirmationCode($pdo, $email, $key)) {
                $confirmNode = $this->dom->createElement('AccountConfirmed');
                $node->appendChild($confirmNode);
                  $this->viewer->login( $email, $password );
            }
            else
                return $this->pageNotFound($x);
        } else if ($_SERVER['REQUEST_URI'] == '/confirm') {
            $confirmNode = $this->dom->createElement('AccountConfirmationSent');
            $node->appendChild($confirmNode);
        }
        else
            return $this->pageNotFound($x);



        $output = $x->transform('AccountConfirmation', $node);
        $this->display->appendOutput($output);
    }

    private function pageNotFound($x) {
        $node = $this->dom->createElement('PageNotFound');
        $output = $x->transform('PageNotFound', $node);
        $this->display->appendOutput($output);
    }

    private function deleteOldEntries($pdo, $user_id) {

        $query_delete = $pdo->prepare('DELETE from confirm WHERE user_id= :user_id');
        $query_delete->bindParam(':user_id', $user_id);
        $query_delete->execute();
    }

    private function activateUserAccount($pdo, $user_id) {
        $query_activate = $pdo->prepare('UPDATE users SET active=true WHERE user_id= :user_id');
        $query_activate->bindParam(':user_id', $user_id);
        $query_activate->execute();
    }

    private function validConfirmationCode($pdo, $email, $key) {

        $query = $pdo->prepare('SELECT user_id, expiration_date FROM confirm WHERE email = :email AND key = :key');
        $query->bindParam(':email', $email);
        $query->bindParam(':key', $key);
        $query->execute();
        $row = $query->fetch(PDO::FETCH_ASSOC);
        $expired = strtotime(date("Y-m-d H:i:s")) > strtotime($row['expiration_date']);

        $u_id = $row['user_id'];
        if ($u_id && !$expired) { //valid
            $this->deleteOldEntries($pdo, $u_id);
            $this->activateUserAccount($pdo, $u_id);
            return true;
        }
        return false;
    }

}

?>
