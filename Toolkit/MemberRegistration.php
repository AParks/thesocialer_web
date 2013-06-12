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
  
  public function __construct( ) { }
  
  public function setDetails( $firstName, $lastName, $emailAddress, $dob, $gender, $password ) {
    $this->firstName = $firstName;
    $this->lastName = $lastName;
    $this->emailAddress = $emailAddress;
    $this->dob = $dob;
    $this->gender = $gender;
    $this->password = $password;
  }
  
  public function setCollege( $college ) {
    $this->college = $college;
  }

  public function setLocation( $loc ) {
    $this->location = $loc;
  }
  
  public function complete( ) {
    $this->verify( );
    
    $pdo = sPDO::getInstance( );
    $query = $pdo->prepare( 'SELECT register( :firstname, :lastname, :emailaddress, :dob, :password, :gender );' );
    //error_log($this->firstName.' - '.$this->lastName.' - '.$this->emailAddress.' - '.$this->dob.' - '.$this->password.' - '.$this->gender);
    $query->bindValue( ':firstname', $this->firstName ); 
    $query->bindValue( ':lastname', $this->lastName );
    $query->bindValue( ':emailaddress', $this->emailAddress );
    $query->bindValue( ':dob', $this->dob );
    $query->bindValue( ':password', $this->password );
    $query->bindValue( ':gender', $this->gender ); 
    $query->execute( );
    $userId = $query->fetchColumn( 0 );

    $user = new Member($userId);
    if($this->college) {
      $col = array( "College" => $this->college);
      $user->update($col);
    }
    if($this->location) {
      $loc = array( "Location" => $this->location);
      $user->update($loc);
    }

    return $userId;
  }
  
  private function verify( ) {
    return true;
  }
}
