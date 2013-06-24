<?php

class AccountConfirmation extends ApplicationBase {

    public function execute() {
        $x = XSLTransformer::getInstance();
        $node = $this->dom->createElement('AccountConfirmation');

        if (isset($_GET['key']) && isset($_GET['email'])) {
            $pdo = sPDO::getInstance();
            $query = $pdo->prepare('SELECT user_id FROM confirm WHERE email = :email AND key = :key');
            $query->bindParam(':email', $_GET['email']);
            $query->bindParam(':key', $_GET['key']);
            $query->execute();
            $row = $query->fetch(PDO::FETCH_ASSOC);

            //activate user account
            if ($row['user_id']) { //valid confirmation code
                $query_activate = $pdo->prepare('UPDATE users SET active=true WHERE user_id= :user_id');
                $query_activate->bindParam(':user_id', $row['user_id']);
                $query_activate->execute();

                $confirmNode = $this->dom->createElement('AccountConfirmed');
                $node->appendChild($confirmNode);
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

}

?>
