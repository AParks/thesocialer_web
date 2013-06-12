<html>
<head>
<link rel="stylesheet" href="/Static/CSS/bootstrap.css" />
</head>
<body>
<?php
require_once('../AutoLoader.php');

$query = sPDO::getInstance( )->prepare( 'SELECT COUNT(*) as total, COUNT(DISTINCT(ip_address)) as uniques FROM splash_views' );
$query->execute( );
$row = $query->fetch(PDO::FETCH_OBJ);
echo "Total views: " . $row->total;
echo "<br />\nUniques: " . $row->uniques . "<br />\n";


$query = sPDO::getInstance( )->prepare( 'SELECT email_address, entered_at FROM splash_emails ORDER BY entered_at DESC' );
$query->execute( );
$emails = $query->fetchAll( PDO::FETCH_OBJ );
echo '<table class="table table-striped table-bordered table-condensed" style="width: 500px; margin: 10px;">';
echo '<thead><tr><th>Email</th><th>Entered At</th></tr></thead>';
foreach ( $emails as $email )
{
  echo '<tr>';
  echo "<td>" . htmlentities( $email->email_address ) . '</td><td>' . date( 'h:ia m/d/y', strtotime( $email->entered_at ) ). '</td>';
  echo '</tr>';
}

echo '</table>';
?>
</body>
</html>
