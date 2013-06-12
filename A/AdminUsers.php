<html>
<head>
<link rel="stylesheet" href="/Static/CSS/bootstrap.css" />
<link rel="stylesheet" href="/Static/CSS/AdminUsers.css" />
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script>  
<script>
	function validateForm() {
		var good = true;
		var errmsg = "";
		// Check that fields aren't empty
		/*if($('#file').val()=="") {
			errmsg += "Choose a picture\n";
			good = false;
		}*/
		/*if($('#new_location').find('input[name=loc_name]').val()=="") {
			errmsg += "Missing location name\n";
			good = false;
		}
		if($('#new_location').find('input[name=address]').val()=="") {
			errmsg += "Missing address\n";
			good = false;
		}
		if($('#new_location').find('input[name=description]').val()=="") {
			errmsg += "Missing description\n";
			good = false;
		}*/
		/*if($('input[name=website]').val()=="") {
			errmsg += "Missing website\n";
			good = false;
		}*/
		
		if(!good) {
			alert(errmsg);
			return false;
		}


		return true;
	}
  $(function(){
    $('a.delete').click(function(){
      if(confirm("Are you sure you want to delete this user? This can't be undone.")){
	      $.ajax( {
	        url: '/A/AdminUsersJSON.php',
	        type: 'POST',
	        data: { action: 'delete', userId: $(this).attr('data-user-id') },
	        success: function( data ) {
	        	//alert(data);
				location.reload(true);
	        },
	        error: function( data ) {
				//alert(data.responseText);
	        }
	      });
      }
      return false;
    });
	/*$('#location_table').find('input').attr('disabled','disabled');
	$('#location_table').find('textarea').attr('disabled','disabled');
	$('#location_table').find('input[name=file]').hide();
    $('a.edit').click(function(){
        // TODO change the boxes to inputs with values filled to their previous values
        // somehow make it all in a form so you can do the image upload
    	$(this).parent().parent().find('input').removeAttr('disabled');
    	$(this).parent().parent().find('textarea').removeAttr('disabled');
    	$(this).parent().find('.save').show();
    	$(this).parent().parent().find('input[name=file]').show();
    	$(this).hide();
      });*/
    
  });
</script>
</head>
<body>
<!--<h1>Create a new user:</h1>
<div id="event_container">
  <div id="new_user">
	<form action="/A/AdminUsersJSON.php" method="post" enctype="multipart/form-data" onSubmit="return validateForm()">
	<table class="table table-striped table-bordered table-condensed">

	<tr>
		<th>First Name</th><td><input id="first_name" type="text" name="fname" placeholder="Eric" /></td>
		<td></td>
	</tr>
	<tr>
		<th>Last Name</th><td><input id="last_name" type="text" name="lname" placeholder="OBrien" /></td>
		<td></td>
	</tr>
	<tr>
		<th>Email</th><td><input id="email_address" type="text" name="email" placeholder="eric@thesocialer.com" /></td>
		<td></td>
	</tr>
	<tr>
		<th>Birthday</th><td><input type="text" name="birthday" placeholder="1990-04-19" /></td>
		<td></td>
	</tr>
	<tr>
		<th>Password</th><td><input type="text" name="password" placeholder="hello123" /></td>
		<td></td>
	</tr>
	<tr>
		<th>Gender</th><td><input type="text" name="gender" placeholder="male" /></td>
		<td>male or female</td>
	</tr>
	<tr>
		<th>College</th><td><input type="text" name="college" placeholder="Drexel" /></td>
		<td></td>
	</tr>
	<tr>
		<td colspan="3"><input style="width:100%;" type="submit" name="submit" value="Create User" /></td>
	</tr>
	</table>
	
	<input type="hidden" name="action" value="create" />
	
	</form>
</div>

</div>-->
<div style="clear:both;"></div>
<hr />
<?php

require_once('../AutoLoader.php');
$query = sPDO::getInstance()->prepare( 'SELECT * FROM users ORDER BY signup_date');
$query->execute();

echo '<h1>Current users:</h1>';
echo '<table class="table table-striped table-bordered table-condensed" style="margin: 20px;width: 600px;">';
$count = 0;
echo "<tr><th>Id</th><th>First</th><th>Last</th><th>Email</th><th>Birthday</th><th>Password</th><th>Gender</th><th>Signed Up</th><th></th></tr>";
foreach ( $query->fetchAll(PDO::FETCH_ASSOC) as $user )
{
  $count++;
  echo '<tr style="width:10px">';
  foreach ( $user as $value )
  {
    echo '<td><div style="max-width:200px;overflow:auto">'.$value.'</div></td>';
  }
  echo '<td><a class="delete" data-user-id="' . $user['user_id'] . '" href="#">Delete</a><hr /><br />';
  echo "</td></tr>";
} 
echo "</table>";
echo $count." users in database."
?>

</body>
</html>
