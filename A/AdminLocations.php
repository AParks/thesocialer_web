<html>
<head>
<link rel="stylesheet" href="/Static/CSS/bootstrap.css" />
<link rel="stylesheet" href="/Static/CSS/AdminFeatured.css" />

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
    if($('#new_location').find('input[name=loc_name]').val()=="") {
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
    }
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
      if(confirm("Are you sure you want to delete this location? This can't be undone.")){
        $.ajax( {
          url: '/A/AdminLocationsJSON.php',
          type: 'POST',
          data: { action: 'delete', locId: $(this).attr('data-loc-id') },
	  success: function( data ) {
	    location.reload(true);
	  },
	  error: function( data ) {}
	});
      }
      return false;
    });
    $('#location_table').find('input').attr('disabled','disabled');
    $('#location_table').find('textarea').attr('disabled','disabled');
    $('#location_table').find('input[name=file]').hide();
    $('#location_table').find('input[name=tagdesc]').hide();
    $('a.edit').click(function(){
    	$(this).parent().parent().find('input').removeAttr('disabled');
    	$(this).parent().parent().find('textarea').removeAttr('disabled');
    	$(this).parent().find('.save').show();
    	$(this).parent().parent().find('input[name=file]').show();
    	$(this).hide();
    });

    $('a.tag').click(function(){
        var tagbox = $(this).parent().find('input[name=tagdesc]');
    	// make the input box appear
    	tagbox.show();
    	tagbox.removeAttr('disabled');
    	// make the plus link disappear
    	$(this).hide();
    });
    var deletetagfun = function() {
        var tagbox = $(this).parent();
        var locid = $(this).attr('data-loc-id');
	    var desc =  $(this).attr('data-tag-desc');
	    if (desc == "") return;

	    $.ajax( {
		  url: '/A/TagsJSON.php',
		  type: 'POST',
		  data: { action: 'deletetag', loc_id: locid, tag_desc: desc },
		  success: function( data ) {
			  tagbox.remove();
		  },
		  error: function( data ) {
		    alert(data);
		  }
		});
    };
    $('a.deletetag').click(deletetagfun);
    $('input[name=tagdesc]').keydown(function(event) {
    	  if (event.which == 13) { // enter key pressed
    	  	event.preventDefault();
    	  	var tagbox = $(this);
    	    var locid = $(this).attr('data-loc-id');
    	    var desc = ($(this).val()).toLowerCase();
    	    if (desc == "") return;
    	    //alert(locid+", "+desc);
    	    $.ajax( {
    		  url: '/A/TagsJSON.php',
    		  type: 'POST',
    		  data: { action: 'addtag', loc_id: locid, tag_desc: desc },
    		  success: function( data ) {
    			  tagbox.val("");
    			  tagbox.parent().find('.tag').show();
    			  tagbox.hide();
    			  var markup = '<div style="width:85px;overflow:visible;">'+desc+' <a href="javascript:;" class="deletetag" data-loc-id="'+locid+'" data-tag-desc="' +desc+ '">x</a></div>';
    			  $popup = $('.deletetag[data-loc-id='+locid+']:last').parent().after(markup);
    			  tagbox.parent().parent().find('a.deletetag').click(deletetagfun);
    		  },
    		  error: function( data ) {
    		    alert(data);
    		  }
    		});
    	  }
    	});
    
  });
</script>
</head>
<body>
<h1>Create a new location:</h1>
<div id="event_container">
<div id="new_location">
	<form action="/A/AdminLocationsJSON.php" method="post" enctype="multipart/form-data" onSubmit="return validateForm()">
	<table class="table table-striped table-bordered table-condensed">
	
	<tr>
		<th>Image</th><td><input type="file" name="file" id="file" /> </td>
		<td>The location image</td>
	</tr>
	<tr>
		<th>City ID</th><td><input id="cityId" type="text" name="city_id" value="1" /></td>
		<td>This is 1 by default for Philadelphia</td>
	</tr>
	<tr>
		<th>Name</th><td><input id="locName" type="text" name="loc_name" placeholder="name" /></td>
		<td>The name of the location</td>
	</tr>
	<tr>
		<th>Address</th><td><input id="address" type="text" name="address" placeholder="123 South Street Philadelphia, PA 19104" /></td>
		<td>The street address</td>
	</tr>
	<tr>
		<th>Description</th><td><input type="text" name="description" placeholder="description" /></td>
		<td>The info blurb on the event's page</td>
	</tr>
	<tr>
		<th>Website</th><td><input type="text" name="website" placeholder="www.thesocialer.com" /></td>
		<td>The location's website</td>
	</tr>
	<tr>
		<td colspan="3"><input style="width:100%;" type="submit" name="submit" value="Create Location" /></td>
	</tr>
	</table>
	<!-- Yelp ID - for future potential yelp integration -->
	<input type="hidden" name="yelp_id" value="0"/>
	<input type="hidden" name="action" value="create" />
	
	</form>
</div>

</div>
<div style="clear:both;"></div>
<hr />
<?php

require_once('../AutoLoader.php');
$query = sPDO::getInstance()->prepare( 'SELECT * FROM locations ORDER BY location_name');
$query->execute();

echo '<h1>Current locations:</h1>';
echo '<table id="location_table" class="table table-striped table-bordered table-condensed" style="margin: 20px;width: 1100px;">';
$count = 0;
echo "<tr><th>Loc Id</th><th>City Id</th><th>Location Name</th><th>Street Address</th><th>Description</th><th>Website</th><th>Latitude</th><th>Longitude</th><th>Image</th><th>Tags</th><th></th></tr>";
foreach ( $query->fetchAll(PDO::FETCH_ASSOC) as $location )
{
  $count++;
  echo "<tr>";
  echo "<form class='loc_row' action=\"/A/AdminLocationsJSON.php\" method=\"post\" enctype=\"multipart/form-data\" >";
  
  echo "<td>".$location['location_id']."</td>";
  echo "<td><input style='width:30px;' type=\"text\" name=\"city_id\" value='".$location['city_id']."' /></td>";
  echo "<td><input style='width:100px;' type=\"text\" name=\"loc_name\" value=\"".$location['location_name']."\" /></td>";
  echo "<td><input type=\"text\" name=\"address\" value=\"".$location['street_address']."\" /></td>";
  echo "<td><textarea name=\"description\">".$location['description']."</textarea></td>";
  echo "<td><input style='width:100px;' type=\"text\" name=\"website\" value=\"".$location['website']."\" /></td>";
  //echo "<td><input style='width:30px;' type=\"text\" name=\"yelp_id\" value=\"".$location['yelp_id']."\" /></td>";
  echo "<td><input style='width:85px;' type=\"text\" name=\"latitude\" value=\"".$location['latitude']."\" /></td>";
  echo "<td><input style='width:85px;' type=\"text\" name=\"longitude\" value=\"".$location['longitude']."\" /></td>";
  //echo "<td>".$location['location_image']."</td>";
  echo '<td><input type="file" name="file"/>'.$location['location_image'].'</td>';
  echo '<td>';
	  $tagquery = sPDO::getInstance()->prepare( 'SELECT t.tag_id, t.tag_description FROM tags t, location_tags lt WHERE t.tag_id = lt.tag_id AND lt.location_id = :loc_id');
	  $tagquery->bindValue(':loc_id', $location['location_id'] );
	  $tagquery->execute();
	  // dummy div
	  echo '<div style="width:85px;overflow:visible;display:none;"><a href="javascript:;" class="deletetag" data-loc-id="' . $location['location_id'] . '"></a></div>';
	  foreach ( $tagquery->fetchAll(PDO::FETCH_ASSOC) as $tag ) {
	 	echo '<div style="width:85px;overflow:visible;">'.$tag['tag_description'].' <a href="javascript:;" class="deletetag" data-loc-id="' . $location['location_id'] . '" data-tag-desc="' . $tag['tag_description'] . '">x</a></div>';
	  }
	  echo '<div style="width:85px;overflow:visible;"><input style="width:85px;height:20px;" data-loc-id="' . $location['location_id'] . '" type="text" name="tagdesc"/><a class="tag" href="javascript:;">+</a></div>';
  echo '</td>';
  echo '<td><a class="delete" data-loc-id="' . $location['location_id'] . '" href="#">Delete</a><hr />';
  echo '<a class="edit" data-loc-id="' . $location['location_id'] . '" >Edit</a>';
  echo '<input class="save" style="display:none" type="submit" name="submit" value="Save" />';
  echo '<input type="hidden" name="loc_id" value="'.$location['location_id'].'" />';
  echo '<input type="hidden" name="action" value="update" />';
  echo "</td>";
  echo "</form></tr>";
} 
echo "</table>";
echo $count." locations in database."
?>

</body>
</html>
