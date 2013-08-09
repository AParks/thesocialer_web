<?php

class PhotoUploader
{
  const IMAGE_TYPE_GIF = IMAGETYPE_GIF;
  const IMAGE_TYPE_JPEG = IMAGETYPE_JPEG;
  const IMAGE_TYPE_PNG = IMAGETYPE_PNG;

  const WIDTH_SMALL = 50;
  const HEIGHT_SMALL = 50;

  // Profile width
  const WIDTH_LARGE = 230;
  const HEIGHT_LARGE = 400;

  // User suggestions
  const WIDTH_MEDIUM = 80;
  const HEIGHT_MEDIUM = 80;

  protected $supportedImages = array( self::IMAGE_TYPE_GIF, self::IMAGE_TYPE_JPEG, self::IMAGE_TYPE_PNG ); 
  protected $imageType;
  protected $photo;

  protected function validate( )
  {
      if($this->photo['error'] != 0){
          $error = $this->codeToMessage($this->photo['error']);
          error_log(ini_get("upload_max_filesize"));
          throw new Exception($error);

    //die($error);
      }
    $details = getimagesize( $this->photo['tmp_name'] );

    if ( $details === false )
    {
      error_log('here are the details of the photo:');  
      error_log(print_r($this->photo, true));  
      throw new Exception( 'Invalid photo.' );
    }

    if ( in_array( $details[2], $this->supportedImages ) === false )
    {
      throw new Exception( 'Unsupported photo type.' );
    }

    $this->imageType = $details[2];
  }
   private function codeToMessage($code) 
    { 
        switch ($code) { 
            case 1: 
                $message = "The uploaded file exceeds the upload_max_filesize directive in php.ini"; 
                break; 
            case 2: 
                $message = "The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form"; 
                break; 
            case 3: 
                $message = "The uploaded file was only partially uploaded"; 
                break; 
            case 4: 
                $message = "No file was uploaded"; 
                break; 
            case 5: 
                $message = "Missing a temporary folder"; 
                break; 
            case 6: 
                $message = "Failed to write file to disk"; 
                break; 
            case 7: 
                $message = "File upload stopped by extension"; 
                break; 

            default: 
                $message = "Unknown upload error"; 
                break; 
        } 
        return $message; 
    } 

  public function add( $photo )
  {
    $this->photo = $photo;
    $this->validate( );

    $fileName = $this->generateName();
    $path = $_SERVER['DOCUMENT_ROOT'] . '/Photos/Large/' . $fileName;

    $source = $this->getSource($this->photo['tmp_name']);
    $largeDimensions = $this->getLargeDimensions($source);
    $this->save($source, $_SERVER['DOCUMENT_ROOT'] . '/Photos/Originals/' . $fileName); 
    $this->save($this->resize($source, $largeDimensions['width'], $largeDimensions['height']), $path);

    $mediumPath = $_SERVER['DOCUMENT_ROOT'] . '/Photos/Medium/' . $fileName;
    $this->save($this->resize($source, self::WIDTH_MEDIUM, self::HEIGHT_MEDIUM), $mediumPath);

    $smallPath = $_SERVER['DOCUMENT_ROOT'] . '/Photos/Small/' . $fileName;
    $this->save($this->resize($source, self::WIDTH_SMALL, self::HEIGHT_SMALL), $smallPath);

    return $fileName;
  }

  protected function getLargeDimensions($source)
  {
    $width = imagesx($source);
    $height = imagesy($source);

    $widthRatio = self::WIDTH_LARGE / $width;
    $newHeight = $height * $widthRatio;

    if ( $newHeight < self::HEIGHT_LARGE )
    {
      return array( 'width' => self::WIDTH_LARGE, 'height' => $newHeight );
    }

    $heightRatio = self::HEIGHT_LARGE / $height;
    $newWidth = $width * $heightRatio;
    return array( 'width' => $newWidth, 'height' => self::HEIGHT_LARGE);
  }

  protected function resize($source, $width, $height, $x = 0, $y = 0, $sourceWidth = null, $sourceHeight = null)
  {
    $sourceWidth = $sourceWidth ? $sourceWidth : imagesx($source);
    $sourceHeight = $sourceHeight ? $sourceHeight : imagesy($source);
    
    $resized = imagecreatetruecolor($width, $height);
    imagecopyresampled($resized, $source, 0, 0, $x, $y, $width, $height, $sourceWidth, $sourceHeight); 
    return $resized;
  }

  protected function save($photo, $fileName)
  {
    /*
    $details = getimagesize($photo);
    switch ( $details[2] )
    {
    case self::IMAGE_TYPE_GIF:
      $source = imagegif($photo, $fileName);
      break;
    case self::IMAGE_TYPE_JPEG:
      $source = imagejpeg($photo, $fileName);
      break;
    case self::IMAGE_TYPE_PNG:
      $source = imagepng($photo, $fileName);
      break;
    }
     */
    $result = imagejpeg($photo, $fileName);
    //var_dump($fileName, $result);
  }

  protected function getSource($file)
  {
    $details = getimagesize($file);
    switch ( $details[2] )
    {
    case self::IMAGE_TYPE_GIF:
      $source = imagecreatefromgif($file);
      break;
    case self::IMAGE_TYPE_JPEG:
      $source = imagecreatefromjpeg($file);
      break;
    case self::IMAGE_TYPE_PNG:
      $source = imagecreatefrompng($file);
      break;
    }

    return $source;
  }

  protected function generateName()
  {
    $fileName = uniqid( 'p_', true ) . '_' . rand( 0, 10000 ); 
    return $fileName . '.jpg'; 
  }

  public function setThumbnail($path, $destination, $x, $y, $width, $height, $destinationWidth, $destinationHeight)
  {
    $source = $this->getSource($path);
    $details = getimagesize($path);
    $resized = $this->resize($source, $destinationWidth, $destinationHeight, $x, $y, $width, $height);
    $this->save($resized, $destination);
    return true;
  }
}
