<?php

class PhotoManager
{
  function getUserPhotos( $userId )
  {
    $photos = array( );

    $pdo = sPDO::getInstance( );
    $query = $pdo->prepare('SELECT photo_id, path, is_default, added_at FROM get_member_photos(:user_id)');
    $query->bindValue(':user_id', $userId);
    $query->execute( );

    foreach ( $query->fetchAll( PDO::FETCH_OBJ ) as $photo )
    {
      $photos[] = new Photo( $photo->photo_id );
    }

    return $photos;
  }
}
