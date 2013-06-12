<html>
<head>
<link rel="stylesheet" href="/Static/CSS/bootstrap.css" />
<link rel="stylesheet" href="/Static/CSS/AdminFeatured.css" />
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script>  
</head>
<body>

<?php

require_once('../AutoLoader.php');

$query = sPDO::getInstance()->prepare( 'SELECT user_id FROM users');
$query->execute();

echo '<h1>User Tags:</h1>';
echo '<table class="table table-striped table-bordered table-condensed" style="margin: 20px;width: 1100px;">';

echo "<tr><th>Name</th><th>Tags</th></tr>";
$users = $query->fetchAll(PDO::FETCH_ASSOC);
foreach ( $users as $userid )
{
	
	$user = new Member($userid['user_id']);
	
	echo "<tr>";
	echo '<td style="width:200px"><a href="/profile/'.$user->userId.'">'.$user->firstName." ".$user->lastName."</a></td>";
	echo "<td>";
	foreach ( $user->tags as $value )
	{
		echo "$value, ";
	}
	echo "</td>";
	echo "</tr>";
}
echo "</table>";

?>
</body>
</html>
