<?php

class Member extends ATransformableObject {

    const GENDER_MALE = 'male';
    const GENDER_FEMALE = 'female';
    const GENDER_BOTH = 'both';
    const TYPE_SIMPLE = 'simple';
    const TYPE_COMPLEX = 'complex';
    const FIELD_FIRST_NAME = 'FirstName';
    const FIELD_LAST_NAME = 'LastName';
    const FIELD_EMAIL = 'Email';
    const FIELD_DOB = 'DOB';
    const FIELD_GENDER = 'Gender';
    const FIELD_COLLEGE = 'College';
    const FIELD_LOCATION = 'Location';
    const FIELD_ABOUT_ME = 'AboutMe';

    protected $fields = array(self::FIELD_FIRST_NAME,
        self::FIELD_LAST_NAME,
        self::FIELD_COLLEGE,
        self::FIELD_LOCATION,
        self::FIELD_ABOUT_ME);
    protected $baseProfileFields = array(self::FIELD_FIRST_NAME,
        self::FIELD_LAST_NAME,
        self::FIELD_EMAIL,
        self::FIELD_DOB,
        self::FIELD_GENDER);
    protected $userId;
    protected $firstName;
    protected $lastName;
    protected $emailAddress;
    protected $dob;
    protected $type;
    protected $age;
    protected $AboutMe;
    protected $Location;
    protected $College;
    protected $friendsWithViewer;
    protected $tags = array();
    protected $publicProperties = array('userId', 'firstName', 'lastName',
        'gender', 'photo', 'age', self::FIELD_ABOUT_ME, self::FIELD_COLLEGE, self::FIELD_LOCATION,
        'friendsWithViewer', 'tags', 'emailAddress', 'dob');

    public function __construct($userId, $type = self::TYPE_COMPLEX) {
        $this->userId = (int) $userId;
        $this->type = $type;
        $this->_load();
    }

    public function getProperties() {
        $properties = array();

        foreach ($this->publicProperties as $property) {
            if (is_scalar($this->$property)) {
                $properties[$property] = $this->$property;
            }
        }

        return $properties;
    }

    private function _load() {
        $config = array();
        $config['appId'] = '327877070671041';
        $config['secret'] = '86ef3bb6572ec448b644513076743896';
        $config['fileUpload'] = true; // optional

        $facebook = new Facebook($config);


// See if there is a user from a cookie
        $fb_user = $facebook->getUser();

        if ($fb_user) {
            try {
                $user_info = $facebook->api('/' . $fb_user);
                $this->firstName = $user_info['first_name'];
                $this->lastName = $user_info['last_name'];
                $this->emailAddress = $user_info['email'];
                $this->dob = $user_info['birthday'];
                $this->gender =  $user_info['gender'];
                date_default_timezone_set("America/New_York");
                $dobDateTime = new DateTime($this->dob);
                $ageDateTime = $dobDateTime->diff(new DateTime());
                $this->age = $ageDateTime->y;
            } catch (FacebookApiException $e) {
                
            }
        } else {
            $pdo = sPDO::getInstance();
            $query = $pdo->prepare('SELECT first_name, last_name, email_address, date_of_birth, gender FROM member_data( :user_id )');
            $query->bindValue(':user_id', $this->userId);

            $query->execute();
            $row = $query->fetch(PDO::FETCH_OBJ);

            if (!$row->email_address) {
                // This causes an issue with the 'what's happening near you' bit on the init page
                // need a way to throw this exception without printing to the output - should be some setting that I can't find
                //throw new Exception( 'Unknown member.' );
            }

            $this->firstName = $row->first_name;
            $this->lastName = $row->last_name;
            $this->emailAddress = $row->email_address;
            $this->dob = $row->date_of_birth;
            $this->gender = $row->gender;
            date_default_timezone_set("America/New_York");
            $dobDateTime = new DateTime($this->dob);
            $ageDateTime = $dobDateTime->diff(new DateTime());
            $this->age = $ageDateTime->y;

            $tagquery = $pdo->prepare('SELECT utp.tag_id, t.tag_description FROM tags t, user_tag_prefs utp WHERE utp.user_id = :user_id AND utp.tag_id = t.tag_id');
            $tagquery->bindValue(':user_id', $this->userId);
            $tagquery->execute();
            foreach ($tagquery->fetchAll(PDO::FETCH_ASSOC) as $tag) {
                $this->tags[$tag['tag_id']] = $tag['tag_description'];
            }

            $query = $pdo->prepare('SELECT profile_field, profile_field_value FROM profile_fields(:user_id)');
            $query->bindValue(':user_id', $this->userId);
            $query->execute();
            foreach ($query->fetchAll(PDO::FETCH_OBJ) as $row) {
                $this->{$row->profile_field} = $row->profile_field_value;
            }

            if ($this->type === self::TYPE_COMPLEX) {
                $this->photo = Photo::getByUser($this->userId, $this->gender);
            }
        }
    }

    public function __get($key) {
        if (in_array($key, $this->publicProperties)) {
            return is_string($this->{$key}) ? htmlentities($this->{$key}, ENT_QUOTES) : $this->{$key};
        }

        throw new Exception('Unknown property.');
    }

    public static function validUserId($userId) {
        $userId = (int) $userId;
        return $userId > 0;
    }

    public function getEmail() {
        return $this->emailAddress;
    }

    public function update($fields) {
        $result = true;

        foreach ($fields as $type => $value) {
            if (in_array($type, $this->baseProfileFields) === false) {
                $result = $this->_update($type, $this->sanitizeInput($type, $value)) && $result;
            }
        }

        if (isset($fields[self::FIELD_FIRST_NAME]) || isset($fields[self::FIELD_LAST_NAME])) {
            $firstName = isset($fields[self::FIELD_FIRST_NAME]) ? trim($fields[self::FIELD_FIRST_NAME]) : $this->firstName;
            $lastName = isset($fields[self::FIELD_LAST_NAME]) ? trim($fields[self::FIELD_LAST_NAME]) : $this->lastName;
            $this->_updateName($firstName, $lastName);
        }

        if (isset($fields[self::FIELD_EMAIL])) {
            $this->_updateEmail(trim($fields[self::FIELD_EMAIL]));
        }
        if (isset($fields[self::FIELD_DOB])) {
            $this->_updateDOB(trim($fields[self::FIELD_DOB]));
        }
        if (isset($fields[self::FIELD_GENDER])) {
            $this->_updateGender(trim($fields[self::FIELD_GENDER]));
        }

        return $result;
    }

    public function updatePassword($fields) {
        $oldpass = $fields['OldPassword'];
        $newpass = $fields['NewPassword'];
        $pdo = sPDO::getInstance();
        $query = $pdo->prepare('SELECT user_id, password FROM users WHERE user_id = :id');
        $query->bindParam(':id', $this->userId);
        $query->execute();
        $row = $query->fetch(PDO::FETCH_ASSOC);
        if ($storedhash = $row['password']) {
            if (Viewer::validatePassword($oldpass, $storedhash)) {
                $newhash = Register::hashPassword($newpass);
                $this->_updatePassword($newhash);
                return true;
            } else {
                return false;
            }
        }
    }

    protected function _updateName($firstName, $lastName) {
        $query = sPDO::getInstance()->prepare('SELECT update_user_info( :user_id, :first_name, :last_name )');
        $query->bindValue(':user_id', $this->userId);
        $query->bindValue(':first_name', $this->sanitizeInput(self::FIELD_FIRST_NAME, $firstName));
        $query->bindValue(':last_name', $this->sanitizeInput(self::FIELD_LAST_NAME, $lastName));
        return $query->execute();
    }

    protected function _updateEmail($email) {
        $query = sPDO::getInstance()->prepare('SELECT update_user_email( :user_id, :email )');
        $query->bindValue(':user_id', $this->userId);
        $query->bindValue(':email', $email);
        return $query->execute();
    }

    protected function _updateDOB($dob) {
        $query = sPDO::getInstance()->prepare('SELECT update_user_dob( :user_id, :dob )');
        $query->bindValue(':user_id', $this->userId);
        $query->bindValue(':dob', $dob);
        return $query->execute();
    }

    protected function _updateGender($gender) {
        $query = sPDO::getInstance()->prepare('SELECT update_user_gender( :user_id, :gender )');
        $query->bindValue(':user_id', $this->userId);
        $query->bindValue(':gender', $gender);
        return $query->execute();
    }

    protected function _updatePassword($pass) {
        $query = sPDO::getInstance()->prepare('SELECT update_user_password( :user_id, :password )');
        $query->bindValue(':user_id', $this->userId);
        $query->bindValue(':password', $pass);
        return $query->execute();
    }

    protected function _update($field, $value) {
        $query = sPDO::getInstance()->prepare('SELECT set_profile_field( :user_id, :field, :value )');
        $query->bindValue(':user_id', $this->userId);
        $query->bindValue(':field', $field);
        $query->bindValue(':value', $value);
        return $query->execute();
    }

    public function addTag($tag) {
        $query = sPDO::getInstance()->prepare('SELECT user_tag_add( :user_id, :tag )');
        $query->bindValue(':user_id', $this->userId);
        $query->bindValue(':tag', $tag);
        return $query->execute();
    }

    public function delTag($tag) {
        $query = sPDO::getInstance()->prepare('SELECT user_tag_delete( :user_id, :tag )');
        $query->bindValue(':user_id', $this->userId);
        $query->bindValue(':tag', $tag);
        return $query->execute();
    }

    public function likesLocation($locationId) {
        $query = sPDO::getInstance()->prepare('SELECT COUNT(user_id) from location_likes WHERE user_id = :userId AND location_id = :locId');
        $query->bindValue(':userId', $this->userId);
        $query->bindValue(':locId', $locationId);
        $query->execute();
        $num = $query->fetch(PDO::FETCH_NUM);

        return $num[0] == 1;
    }

    protected function sanitizeInput($field, $value) {
        if ($field !== self::FIELD_ABOUT_ME) {
            return preg_replace('[^A-Za-z0-9_-\'" ]', '', $value);
        }

        return $value;
    }

    public function getQuickPicks() {
        $quickPicks = new QuickPicks( );
        return $quickPicks->userQuickPicks($this->userId);
    }

}
