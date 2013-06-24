<?php

class MemberRegistration {

    protected $firstName;
    protected $lastName;
    protected $emailAddress;
    protected $dob;
    protected $college;
    protected $gender;
    protected $password;
    protected $location;
    protected $fb_id;
    protected $active;

    public function __construct($firstName, $lastName, $emailAddress, $dob, $gender, $password, $fb_id, $active) {
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->emailAddress = $emailAddress;
        $this->dob = $dob;
        $this->gender = $gender;
        $this->password = $password;
        $this->fb_id = $fb_id;
        $this->active = $active;
    }

    public function setCollege($college) {
        $this->college = $college;
    }

    public function setLocation($loc) {
        $this->location = $loc;
    }

    public function complete() {
        $this->verify();

        $pdo = sPDO::getInstance();
        $query = $pdo->prepare('SELECT register( :firstname, :lastname, :emailaddress, :dob, :password, :gender, :fb_id, :active );');
        $query->bindValue(':firstname', $this->firstName);
        $query->bindValue(':lastname', $this->lastName);
        $query->bindValue(':emailaddress', $this->emailAddress);
        $query->bindValue(':dob', $this->dob);
        $query->bindValue(':password', $this->password);
        $query->bindValue(':gender', $this->gender);
        $query->bindValue(':fb_id', $this->fb_id);
        $query->bindValue(':active', $this->active);

        $query->execute();
        $userId = $query->fetchColumn(0);

        $user = new Member($userId);
        if ($this->college) {
            $col = array("College" => $this->college);
            $user->update($col);
        }
        if ($this->location) {
            $loc = array("Location" => $this->location);
            $user->update($loc);
        }


        return $userId;
    }

    private function verify() {
        return true;
    }

}
