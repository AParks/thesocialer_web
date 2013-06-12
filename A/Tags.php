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
		if($('#file').val()=="") {
			errmsg += "Choose a picture\n";
			good = false;
		}
		if($('input[name=headline]').val()=="") {
			errmsg += "Missing headline text\n";
			good = false;
		}
		if($('input[name=sub_headline]').val()=="") {
			errmsg += "Missing sub_headline text\n";
			good = false;
		}
		if($('input[name=description]').val()=="") {
			errmsg += "Missing event description\n";
			good = false;
		}
		if($('input[name=startMonth]').val()==""
			|| $('input[name=startDay]').val()==""
			|| $('input[name=startYear]').val()==""
			|| $('input[name=endMonth]').val()==""
			|| $('input[name=endDay]').val()==""
			|| $('input[name=endYear]').val()=="") {
			errmsg += "Invalid date info\n";
			good = false;
		}
		if($('input[name=location]').val()=="") {
			errmsg += "Missing event location";
			good = false;
		}
		
		if(!good) {
			alert(errmsg);
			return false;
		}
		var startDate = $('input[name=startMonth]').val()+"/"
					  + $('input[name=startDay]').val()+ "/"
					  + $('input[name=startYear]').val(); 
		$('input[name=startDate]').val(startDate);

		var endDate = $('input[name=endMonth]').val()+"/"
		  + $('input[name=endDay]').val()+ "/"
		  + $('input[name=endYear]').val(); 
		$('input[name=endDate]').val(endDate);

		var markup = "<div style=\"text-align: center;\">"
		  			+ "<img src=\"/Photos/Featured/"+$('#file').val().split('\\').pop()+"\" style=\"width: 400px; height: 200px;\" />"
		  			+ "<div>"
		    		+ "<h1 style=\"font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; font-size: 30px; margin: 10px 0;font-weight: bold;\">"
		    		+ $('input[name=headline]').val() + "</h1>"
		    		+ "<div class='subhead'>"+$('input[name=sub_headline]').val()+"<br />"
		    		+ $('input[name=extra_info]').val()+"</div>"
		  			+ "</div></div>";
		$('input[name=markup]').val(markup);

		return true;
	}
  $(function(){
    $('a.delete').click(function(){
      if(confirm("Are you sure you want to delete this tag? This can't be undone.")){
	      $.ajax( {
	        url: '/A/TagsJSON.php',
	        type: 'POST',
	        data: { action: 'deletetagtype', tag_id: $(this).attr('data-tag-id') },
	        success: function( data ) {
				location.reload(true);
	        }
	      });
      }
      return false;
    });

    $('#emailbutton').click(function(){
     var thisrow = $(this).parent().parent();
     var title = thisrow.find('h1').html();
     var desc = thisrow.find('.subhead').html();
      $.ajax( {
        url: '/A/FeaturedJSON.php',
        type: 'POST',
        data: { action: 'email',
            	toemail: $('input[name=toemail]').val(),
            	subject: "The Socialer - " + title, 
                description: desc }, 
        success: function( data ){ alert(data); }
      });
    });

    var headline = document.getElementById('headinput');
	var headpreview = document.getElementById('headline');
	headline.onmouseup = headline.onkeyup = headline.onkeydown = function () {
		headpreview.innerHTML = headline.value;
	};
	var subheadline = document.getElementById('subheadinput');
	var subheadpreview = document.getElementById('sub_headline');
	subheadline.onmouseup = subheadline.onkeyup = subheadline.onkeydown = function () {
		subheadpreview.innerHTML = subheadline.value;
	};
	var extrainfo = document.getElementById('extrainput');
	var extrapreview = document.getElementById('extra_info');
	extrainfo.onmouseup = extrainfo.onkeyup = extrainfo.onkeydown = function () {
		extrapreview.innerHTML = extrainfo.value;
	};
    
    
  });
</script>
</head>
<body>

<?php

require_once('../AutoLoader.php');

$query = sPDO::getInstance()->prepare( 'SELECT * FROM location_tags');
$query->execute();

echo '<h1>Tags:</h1>';
echo '<table class="table table-striped table-bordered table-condensed" style="margin: 20px;width: 1100px;">';

echo "<tr><th>tag_id</th><th>location_id</th></tr>";
foreach ( $query->fetchAll(PDO::FETCH_ASSOC) as $tag )
{
	echo "<tr>";
	foreach ( $tag as $value )
	{
		echo "<td>$value</td>";
	}
	echo "</tr>";
}
echo "</table>";


$query = sPDO::getInstance()->prepare( 'SELECT * FROM tags');
$query->execute();

echo '<h1>Tag Types:</h1>';
echo '<table class="table table-striped table-bordered table-condensed" style="margin: 20px;width: 1100px;">';

echo "<tr><th>Id</th><th>Description</th><th></th></tr>";
foreach ( $query->fetchAll(PDO::FETCH_ASSOC) as $tag )
{
  echo "<tr>";
  foreach ( $tag as $value )
  {
    echo "<td>$value</td>";
  }
  echo '<td><a class="delete" data-tag-id="' . $tag['tag_id'] . '"  href="#">Delete</a><hr /><br />';
  echo "";
  echo "</td></tr>";
} 
echo "</table>";
?>
</body>
</html>
