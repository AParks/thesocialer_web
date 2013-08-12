<html>
    <head>
        <link rel="stylesheet" href="/Static/CSS/bootstrap.css" />
        <link rel="stylesheet" href="/Static/CSS/AdminFeatured.css" />
        <link rel="stylesheet" href="/Static/CSS/Plugins/jquery.ui.all.css" />

        <script src="/Static/JavaScript/Plugins/jquery-1.9.1.js"></script>
        <script src="/Static/JavaScript/Plugins/jquery.ui.core.js"></script>
        <script src="/Static/JavaScript/Plugins/jquery.ui.widget.js"></script>
        <script src="/Static/JavaScript/Plugins/jquery.ui.datepicker.js"></script>



      <!--  <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script> -->
        <style>
            .datepicker{
                width: 100px;
            }
            #events input[type=text]{
                border: none; 
                webkit-box-shadow: none;
                -moz-box-shadow: none;
                box-shadow: none;
                -webkit-transition: none;
                -moz-transition: none;
                -o-transition: none;
                background-color: transparent;
            }
            #events input[type=text]:hover{
                background-color: #ffffff;
                border: 1px solid #cccccc;
                -webkit-box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.075);
                -moz-box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.075);
                box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.075);
                -webkit-transition: border linear 0.2s, box-shadow linear 0.2s;
                -moz-transition: border linear 0.2s, box-shadow linear 0.2s;
                -o-transition: border linear 0.2s, box-shadow linear 0.2s;
                transition: border linear 0.2s, box-shadow linear 0.2s;
            }
        </style>
        <script>
            function validateForm() {
                var good = true;
                var errmsg = "";

                // Check that fields aren't empty
                if ($('#file').val() == "") {
                    errmsg += "Choose a picture\n";
                    good = false;
                }
                if ($('#host').val() == "") {
                    errmsg += "Provide a host\n";
                    good = false;
                }
                if ($('input[name=headline]').val() == "") {
                    errmsg += "Missing headline text\n";
                    good = false;
                }
                if ($('input[name=sub_headline]').val() == "") {
                    errmsg += "Missing sub_headline text\n";
                    good = false;
                }
                if ($('input[name=price]').val() == "") {
                    errmsg += "Missing price \n";
                    good = false;
                }
                if (!($('input[name=price]').val().match(/^\d+(\.\d{2})$/))) {
                    errmsg += "Invalid price. Must look like the form '20.00'\n";
                    good = false;
                }

                if ($('textarea[name=description]').val() == "") {
                    errmsg += "Missing event description\n";
                    good = false;
                }

                if ($('input[name=startMonth]').val() == ""
                        || $('input[name=startDay]').val() == ""
                        || $('input[name=startYear]').val() == ""
                        || $('input[name=startTime]').val() == ""
                        || $('input[name=endMonth]').val() == ""
                        || $('input[name=endDay]').val() == ""
                        || $('input[name=endYear]').val() == ""
                        || $('input[name=endTime]').val() == "") {
                    errmsg += "Invalid date info\n";
                    good = false;
                }
                if ($('input[name=location]').val() == "") {
                    errmsg += "Missing event location\n";
                    good = false;
                }
                if (!($('input[name=startTime]').val().match(/^(([01]\d)|(2[0-3])):[0-5]\d$/)) ||
                        !($('input[name=endTime]').val().match(/^([01]\d|2[0-3]):[0-5]\d$/))) {
                    errmsg += "Invalid time format\n";
                    good = false;
                }

                if (!good) {
                    alert(errmsg);

                    return false;
                }
                var startDate = $('input[name=startDate]').val() + " " + $('input[name=startTime]').val();
                $('input[name=startDate]').val(startDate);

                var endDate = $('input[name=endDate]').val() + " " + $('input[name=endTime]').val();
                $('input[name=endDate]').val(endDate);



                //  var markup = "/Photos/Featured/" + $('#file').val().split('\\').pop();


                return true;
            }

            $(function() {
                $(".datepicker").datepicker({ dateFormat: "mm/dd/yy" });

                $('input[type=file]').change(function() {

                    var eventId = $(this).first().parent()
                            .parent().parent()
                            .find('input[key=featured_event_id]').val();
                    $(this).parent().find('input[name=eventId]').val(eventId);

                    $(this).parent().submit();
                    return false;
                });
                $('#events input[type=text], #events textarea').focusout(function() {
                    $.ajax({
                        url: '/featured/json',
                        type: 'POST',
                        data: {
                            action: 'edit',
                            key: $(this).attr('key'),
                            eventId: $(this).first()
                                    .parent().parent()
                                    .find('input[key=featured_event_id]').val(),
                            value: $(this).val()},
                        sucess: function(data) {
                            location.reload(true);
                        }
                    });
                    return false;
                });
                $('a.delete').click(function() {
                    if (confirm("Are you sure you want to delete this event? This can't be undone.")) {
                        $.ajax({
                            url: 'featured/json',
                            type: 'POST',
                            data: {action: 'delete', eventId: $(this).attr('data-event-id')},
                            success: function(data) {
                                location.reload(true);
                            }
                        });
                    }
                    return false;
                });

                $('#emailbutton').click(function() {
                    var thisrow = $(this).parent().parent();
                    var title = thisrow.find('h1').html();
                    var desc = thisrow.find('.subhead').html();
                    $.ajax({
                        url: '/featured/json',
                        type: 'POST',
                        data: {
                            action: 'email',
                            toemail: $('input[name=toemail]').val(),
                            subject: "The Socialer - " + title,
                            description: desc
                        },
                        success: function(data) {
                            alert(data);
                        }
                    });
                });

                var headline = document.getElementById('headinput');
                var headpreview = document.getElementById('headline');
                headline.onmouseup = headline.onkeyup = headline.onkeydown = function() {
                    headpreview.innerHTML = headline.value;
                };
                var subheadline = document.getElementById('subheadinput');
                var subheadpreview = document.getElementById('sub_headline');
                subheadline.onmouseup = subheadline.onkeyup = subheadline.onkeydown = function() {
                    subheadpreview.innerHTML = subheadline.value;
                };
                var extrainfo = document.getElementById('extrainput');
                var extrapreview = document.getElementById('extra_info');
                extrainfo.onmouseup = extrainfo.onkeyup = extrainfo.onkeydown = function() {
                    extrapreview.innerHTML = extrainfo.value;
                };


            });
        </script>
    </head>
    <body>
        <h1>Create a new featured event:</h1>
        <div id="event_container">
            <div id="new_event">
                <form action="/featured/json" method="post" enctype="multipart/form-data" onSubmit="return validateForm()">
                    <table class="table table-striped table-bordered table-condensed">

                        <tr>
                            <th>Image</th><td><input type="file" name="file[]" id="file" multiple/> </td>
                            <td>The featured image</td>
                        </tr>
                        <tr>
                            <th>Headline Text</th><td><input id="headinput" type="text" name="headline" /></td>
                            <td>The main text for the spotlighted event</td>
                        </tr>
                        <tr>
                            <th>Sub headline</th><td><input id="subheadinput" type="text" name="sub_headline" /></td>
                            <td>text to display directly below the headline</td>
                        </tr>

                        <tr>
                            <th>Price</th><td><input id="extrainput" type="text" min="0" name="price" /></td>
                            <td></td>
                        </tr>
                        <tr>
                            <th>Event Type</th>
                            <td>
                                <select type="number" style="width: 80%" name="is_private">
                                    <option value="true" selected="selected">Private</option>
                                    <option value="false">Public</option>
                                </select>
                            </td>
                            <td></td>

                        </tr>
                        <tr>
                            <th>Description</th><td>
                                <textarea name="description"></textarea>
                            </td>
                            <td>The info blurb on the event's page</td>
                        </tr>
                        <tr><th>Start Date (using military time)</th>
                            <td>
                                <input class="date datepicker" type="text" name="startDate" />
                                <input class="date" type="text" name="startTime" placeholder="HH:MM" style="width:70px;"/>
                            </td>
                            <td>The event's start date and time</td>
                        </tr>
                        <tr><th>End Date</th>
                            <td>
                                <input class="date datepicker" type="text" name="endDate" />
                                <input class="date" type="text" name="endTime" placeholder="HH:MM" style="width:70px;"/>

                            </td>
                            <td>The event's end date</td>
                        </tr>
                        <tr>
                            <th>Location</th><td><input type="text" name="location" placeholder="123 South Street Philadelphia, PA 19104" /></td>
                            <td>The map location to display on the event page</td>
                        </tr>
                        <tr>
                            <th>Hosted By</th><td><input type="number" style="width: 80%; height:80%" id="host" name="host" placeholder="UserID" /></td>
                            <td>Provide the user id number found in the url socialer.com/profile/userid</td>
                        </tr>
                        <tr>
                            <th>Priority</th><td><input type="number" style="width: 80%; height:100%" id="priority" name="priority" value=0 /></td>
                            <td>Events with higher priority will appear first</td>
                        </tr>
                        <tr> 
                            <th>Spots</th><td><input type="number" style="width: 80%; height:100%" id="spots" name="spots" value=0 /></td>
                            <td>total number of tickets/spots to sell</td>
                        </tr>
                        <tr>
                            <td colspan="3"><input style="width:100%;" type="submit" name="submit" value="Create Event" /></td>
                        </tr>
                    </table>
                    <input type="hidden" name="startDate" value="" />
                    <input type="hidden" name="endDate" value="" />
                    <input type="hidden" name="markup" value="" />
                    <input type="hidden" name="action" value="create" />

                </form>
            </div>

            <div id="event_preview">
                <div id="preview_label">featured preview</div>
                <div id="image_placeholder">Your Image 400x200</div>
                <div>
                    <h1 id="headline">Headline text!</h1>
                    <div id="sub_headline">Take a weekend somewhere for 25% off.  Bring 3 friends and get 50% off!</div>
                    <div id="extra_info">May 16 through May 20</div>
                </div>
            </div>
        </div>
        <div style="clear:both;"></div>
        <hr />
        <?php
        require_once('../AutoLoader.php');
        $query = sPDO::getInstance()->prepare('SELECT * FROM featured_events ORDER BY starts_at DESC');
        $query->execute();
        echo '<h1>Current featured events:</h1>';
        echo '<table id="events" class="table table-striped table-bordered table-condensed" style="margin: 20px;width: 1100px;">';

        echo "<tr><th>Id</th><th>Description</th><th>Start Date</th><th>End Date</th><th>Markup</th><th>Location</th><th>Price</th><th>Headline</th><th>Is private</th><th>SubHeadline</th><th>Host</th><th>Priority</th><th>Total Spots</th><th>Spots Purchased</th><th></th></tr>";
        foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $featured) {
            echo "<tr>";
            foreach ($featured as $key => $value) {
                if ($key == 'markup') {
                    echo "<td>";
                    echo "<form action='/featured/json' method='post' enctype='multipart/form-data' >";
                    echo "<input type='file' name='file[]' id='file' multiple>$value</>";
                    echo '<input type="hidden" name="eventId" />';
                    echo "<input type='hidden' name='action' value='edit' />";
                    echo "<input type='hidden' name='key' value='$key' />";
                    echo "<input type='text' name='$key' key='$key' value='$value' />";
                    echo "</form></td>";
                } else if ($key == 'description')
                    echo "<td><textarea  name='$key' key='$key' >$value </textarea></td>";
                else
                    echo "<td><input type='text' name='$key' key='$key' value='$value' /> </td>";
            }
            echo '<td><a class="delete" data-event-id="' . $featured['featured_event_id'] . '" href="#">Delete</a><hr /><br />';
            echo "Send a test email:<br /><input type='email' name='toemail' placeholder='user@gmail.com'/><input id='emailbutton' type='button' value='Send'>";
            echo "</td></tr>";
        }
        echo "</table>";
        ?>

    </body>
</html>
