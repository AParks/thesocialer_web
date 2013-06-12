<?php
require_once('../AutoLoader.php');
ini_set('display_errors', 1);

$users = new UserSearch();
$otherUserInfo = array();
$loc = array( "Location" => "Philadelphia" );
foreach ( $users->query("") as $otherUser ) {
  //$otherUser->update($loc);
}
echo "This script is disabled for now. It would have forcibly set everyone's location to Philadelphia.";

?>
