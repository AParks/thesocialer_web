<?php
// only enable this if you want eveeything to break
//ini_set('display_errors',1);
class PhotoManagerJSON extends ApplicationBase 
{
  const ACTION_UPLOAD = 'upload';
  const ACTION_SET_THUMBNAIL = 'setThumbnail';
  const ACTION_DELETE = 'deletephoto';
  const ACTION_MAKE_DEFAULT = 'makedefault';

  function execute( )
  {
    switch ( $this->requestValues[0] )
    {
    case self::ACTION_UPLOAD:
      $this->upload( $_FILES['photo'] );
      break;
    case self::ACTION_SET_THUMBNAIL:
      $this->setThumbnail( $_POST['photoId'], $_POST['x'], $_POST['y'], $_POST['width'], $_POST['height'] );
      break;
    case self::ACTION_DELETE:
      $this->deletePhoto( $_POST['photoId'] );
      break;
    case self::ACTION_MAKE_DEFAULT:
      $this->makeDefault( $_POST['photoId'] );
      break;
    }

    exit;
  }

  function upload( $upload )
  {
    $photoUploader = new PhotoUploader();
    $file = $photoUploader->add($upload);
    $photo = Photo::create($this->viewer->user, $file);

    echo json_encode(array('file' => $file, 'photoId' => $photo->photoId));
  }
  
  function deletePhoto( $photoId ) {
  	$photo = new Photo($photoId);
  
  	if ( $photo->userId !== $this->viewer->user->userId )
  	{
  		throw new Exception();
  	}
  	
  	$pdo = sPDO::getInstance();
  	$delquery = $pdo->prepare('DELETE FROM photos where user_id = :user_id AND photo_id = :photo_id');
  	$delquery->bindValue( ':user_id', $photo->userId );
  	$delquery->bindValue( ':photo_id', $photo->photoId );
  	
  	$delquery->execute();
  	
  	foreach ( $photo->paths as $path )
  	{
  		unlink($_SERVER['DOCUMENT_ROOT'].$path);
  	}
  	
  }
  
  function makeDefault($photoId) {
  	$photo = new Photo($photoId);
  	
  	if ( $photo->userId !== $this->viewer->user->userId )
  	{
  		throw new Exception();
  	}
  	
  	$pdo = sPDO::getInstance();
  	$query = $pdo->prepare("UPDATE photos SET is_default = 'f' WHERE user_id = :user_id AND is_default = 't' ");
  	$query->bindValue( ':user_id', $photo->userId );
  	$query->execute();
  	
  	$query = $pdo->prepare("UPDATE photos SET is_default = 't' WHERE user_id = :user_id AND photo_id = :photo_id ");
  	$query->bindValue( ':user_id', $photo->userId );
  	$query->bindValue( ':photo_id', $photo->photoId );
  	$query->execute();
  	 
  }

  function setThumbnail( $photoId, $x, $y, $width, $height ) {
    $photo = new Photo($photoId);

    if ( $photo->userId !== $this->viewer->user->userId )
    {
      throw new Exception();
    }

    $result = $photo->reThumbnail($x, $y, $width, $height);
    echo json_encode(array('result' => $result));
  }
}
