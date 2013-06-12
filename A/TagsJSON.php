<?php
require_once('../AutoLoader.php');
ini_set('display_errors',1);

if ( $_POST['action'] === 'deletetag' )
{
	$query = sPDO::getInstance()->prepare('DELETE FROM location_tags WHERE location_id = :loc_id AND tag_id = get_tag_id(:tag_desc)');
	$query->bindValue(':loc_id', $_POST['loc_id'] );
	$query->bindValue(':tag_desc', $_POST['tag_desc'] );
	$query->execute();
}

if ( $_POST['action'] === 'deletetagtype' )
{
	$query = sPDO::getInstance()->prepare('DELETE FROM location_tags WHERE tag_id = :tag_id');
	$query->bindValue(':tag_id', $_POST['tag_id'] );
	$query->execute();
	
	$query = sPDO::getInstance()->prepare('DELETE FROM tags WHERE tag_id = :tag_id');
	$query->bindValue(':tag_id', $_POST['tag_id'] );
	$query->execute();
}

if ( $_POST['action'] === 'addtag' )
{
   	$query = sPDO::getInstance()->prepare('SELECT new_location_tag( :loc_id, :tag_desc)');
   	$query->bindValue(':loc_id', $_POST['loc_id']);
   	$query->bindValue(':tag_desc', $_POST['tag_desc']);
   	$query->execute();
}

?>