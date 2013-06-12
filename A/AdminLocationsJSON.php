<?php
require_once('../AutoLoader.php');
ini_set('display_errors',1);

if ( $_POST['action'] === 'delete' ) {
  $query = sPDO::getInstance()->prepare('DELETE FROM location_attendees WHERE location_id = :loc_id');
  $query->bindValue(':loc_id', $_POST['locId'] );
  $query->execute();
  
  $query = sPDO::getInstance()->prepare('DELETE FROM location_tags WHERE location_id = :loc_id');
  $query->bindValue(':loc_id', $_POST['locId'] );
  $query->execute();
  
  $query = sPDO::getInstance()->prepare('DELETE FROM location_comments WHERE location_id = :loc_id');
  $query->bindValue(':loc_id', $_POST['locId'] );
  $query->execute();
  
  $query = sPDO::getInstance()->prepare('DELETE FROM locations WHERE location_id = :loc_id');
  $query->bindValue(':loc_id', $_POST['locId'] );
  $query->execute();
}

if ( $_POST['action'] === 'create' ) {
  if (($_FILES["file"]["size"]/1024 < 20000)) { // filesize limit 2MB
    if ($_FILES["file"]["error"] > 0) {
      echo "No image uploaded - you can add one later.";
      echo "<br /><a href=\"/A/AdminLocations.php\">Go back to the locations manager.</a>";
      //echo "Error Code: " . $_FILES["file"]["error"] . "<br />";
    }
    else {
      echo "Upload: " . $_FILES["file"]["name"] . "<br />";
      echo "Type: " . $_FILES["file"]["type"] . "<br />";
      echo "Size: " . ($_FILES["file"]["size"] / 1024) . " Kb<br />";
      //echo "Temp file: " . $_FILES["file"]["tmp_name"] . "<br />";
      
      if (file_exists("/var/www/Photos/Locations/" . $_FILES["file"]["name"])) {
	//echo $_FILES["file"]["name"] . " already exists. ";
      }
      else {
	move_uploaded_file($_FILES["file"]["tmp_name"],"/var/www/Photos/Locations/" . $_FILES["file"]["name"]);
	echo "Stored in: " . "/var/www/Photos/Locations/" . $_FILES["file"]["name"];
      }
      echo "<br /><a href=\"/A/AdminLocations.php\">Go back to the locations manager.</a>";
    }
    
    $query = sPDO::getInstance()->prepare('SELECT insert_location( :cityId, :locName, :streetAddress, :description, :website, :yelpId, :locimg )');
    $query->bindValue(':cityId', stripslashes($_POST['city_id']));
    $query->bindValue(':locName', stripslashes($_POST['loc_name']));
    $query->bindValue(':streetAddress', stripslashes($_POST['address']));
    $query->bindValue(':description', str_replace('&', '&amp;', $_POST['description']) );
    $query->bindValue(':website', str_replace('&', '&amp;', $_POST['website']) );
    $query->bindValue(':yelpId', stripslashes($_POST['yelp_id']));
    $query->bindValue(':locimg', $_FILES["file"]["name"]);
    $query->execute();
  }
  else{ echo "File too big. It should be under 2MB";  }	
}

if ( $_POST['action'] === 'update' ) {
  if (($_FILES["file"]["size"]/1024 < 20000) && $_FILES["file"]["error"] <= 0) {
    if (file_exists("/var/www/Photos/Locations/" . $_FILES["file"]["name"])) {
      //echo $_FILES["file"]["name"] . " already exists. ";
    }
    else {
      move_uploaded_file($_FILES["file"]["tmp_name"],"/var/www/Photos/Locations/" . $_FILES["file"]["name"]);
      echo "Stored in: " . "/var/www/Photos/Locations/" . $_FILES["file"]["name"];
    }
    $query = sPDO::getInstance()->prepare('UPDATE locations SET city_id = :cityId, location_name = :locName, street_address = :streetAddress, description = :description, website = :website, yelp_id = :yelpId, latitude = :latitude, longitude = :longitude, location_image = :locimg WHERE location_id = :loc_id');
    $query->bindValue(':cityId', stripslashes($_POST['city_id']));
    $query->bindValue(':locName', stripslashes($_POST['loc_name']));
    $query->bindValue(':streetAddress', stripslashes($_POST['address']));
    $query->bindValue(':description', str_replace('&', '&amp;', $_POST['description']) );
    $query->bindValue(':website', str_replace('&', '&amp;', $_POST['website']) );
    $query->bindValue(':yelpId', "0");
    $query->bindValue(':latitude', $_POST['latitude'] );
    $query->bindValue(':longitude', $_POST['longitude'] );
    $query->bindValue(':locimg', $_FILES["file"]["name"]);
    $query->bindValue(':loc_id', $_POST['loc_id'] );
    $query->execute();
  }
  else {
    $query = sPDO::getInstance()->prepare('UPDATE locations SET city_id = :cityId, location_name = :locName, street_address = :streetAddress, description = :description, website = :website, yelp_id = :yelpId, latitude = :latitude, longitude = :longitude WHERE location_id = :loc_id');
    $query->bindValue(':cityId', stripslashes($_POST['city_id']));
    $query->bindValue(':locName', stripslashes($_POST['loc_name']));
    $query->bindValue(':streetAddress', stripslashes($_POST['address']));
    $query->bindValue(':description', str_replace('&', '&amp;', $_POST['description']) );
    $query->bindValue(':website', str_replace('&', '&amp;', $_POST['website']) );
    $query->bindValue(':yelpId', "0");
    $query->bindValue(':latitude', $_POST['latitude'] );
    $query->bindValue(':longitude', $_POST['longitude'] );
    $query->bindValue(':loc_id', $_POST['loc_id'] );
    $query->execute();
  }
  echo "<a href=\"/A/AdminLocations.php\">Go back to the locations manager.</a>";
}

?>
