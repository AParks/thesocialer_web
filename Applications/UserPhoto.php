<?php

class UserPhoto extends ApplicationBase
{
  function execute()
  {
    $userId = $this->requestValues[0];

    $size = Photo::SIZE_MEDIUM;

    if ( isset( $this->requestValues[1] ) )
    {
      $size = $this->requestValues[1];
    }

    $user = new Member( $userId, Member::TYPE_SIMPLE );
    $this->photo = Photo::getByUser( $userId, $user->gender );
    $redirect = $this->photo->paths[$size]; 
    $this->redirect($redirect);
  }
}
