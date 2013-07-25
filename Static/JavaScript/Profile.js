var Profile = function(user) {
    var attendanceManager = new AttendanceManager();
    var $pastEventSkeleton = $('#PastEventSkeleton').removeAttr('id').remove();
    var $likedLocationSkeleton = $('#LikedLocationSkeleton').removeAttr('id').remove();
    var $locationNamePopup = $('#LocationNamePopup');
    var days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
    var months = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];

    var $photos = $('img', 'div.PhotoThumbs');
    var photoCount = $photos.length;
    var $popup;
    var $activePhoto;

    function init( ) {
        if (isViewer( )) {
            initEditProfile( );
        }
        else {
            $('#FriendRequest').bind('click', function( ) {
                FriendRequestManager.showPopup({name: user.firstName, userId: user.userId});
            });
        }

        getUserPastEvents();
        getPastHostEvents();
        getUserLikedLocations();
        $photos.bind('click', showPhoto);

        $('#SendMessage').bind('click', showSendMessage);
        $('textarea').autosize();



        $('i.icon-edit').bind('click', function() {
            var save = $(this).next();
            save.css('display', 'inline');
            $(this).css('display', 'none');
            $text = $(this).closest('.ProfileBox').find('textarea');
            $text.css('display', 'inline');
            $text.removeAttr('readonly');
            $text.addClass('editable');


        });
    }

    function showSendMessage( ) {
        MessageSender.showPopup(user);
        return false;
    }

    function initEditProfile( ) {
        
        $('i.icon-save').bind('click', function( ) {
            $profilebox = $(this).closest('.ProfileBox');
            save($profilebox);
        });

        $('#UploadPhotoLink').bind('click', function( ) {
            var pu = new PhotoUploader();
            pu.start();
            return false;
        });
    }

    function save(profilebox) {

        var params = {};
        profilebox.find('textarea').each(function( ) {
            params[$(this).attr('data-field')] = $(this).val();
        });

        $.ajax({
            url: '/profile/json/save',
            type: 'POST',
            dataType: 'json',
            data: params,
            success: function(data) {
                if (data.result) {
                    // profilebox.find('#edit').prepend("<i class='saved'>Saved! </i>");

                    profilebox.find('textarea').each(function( ) {
                        $(this).removeClass('editable');
                        $(this).attr('readonly', 'false');

                        if ($(this).attr('data-field') == 'FirstName' ||
                                $(this).attr('data-field') == 'LastName') {
                            $(this).css('display', 'none');

                            $first = $('textarea[data-field=FirstName]');
                            $last = $('textarea[data-field=LastName]');
                            $('div.ProfileTitle').text($first.val() + " " + $last.val());
                        }
                    });
                    profilebox.find('i.icon-save').css('display', 'none');
                    profilebox.find('i.icon-edit').css('display', 'inline');
                    //$('i.saved').remove();


                }
            }
        });
    }

    function isViewer( ) {
        return Viewer.userId === user.userId;
    }

    function getUserLikedLocations() {
        Main.fetchFromServer('/locations/json/likedLocations', {userId: user.userId}, receiveUserLikedLocations);
    }

    function receiveUserLikedLocations(response) {
        var $likedLocations = $('#LikedLocations');
        var newLoc;
        var loc;
        var locLink;
        var hasLikedLocs = false;

        for (var i = 0, len = response.likedLocations.length; i < len; i++) {
            loc = response.likedLocations[i];
            locLink = '/location/' + loc.id + '/';

            newLoc = $likedLocationSkeleton.clone(true);

            newLoc.find('img').attr('src', '/Photos/Locations/' + loc.image);
            newLoc.find('a').attr('href', locLink);

            if (i + 1 === len) {
                newLoc.addClass('last');
            }

            newLoc.appendTo($likedLocations);


            var nameDiv = $('<div class="LocationName"></div>');
            nameDiv.hide().addClass('mouseOver');
            nameDiv.text(loc.name);
            nameDiv.css('left', -1 * (nameDiv.outerWidth( ) - newLoc.outerWidth( )) / 2);
            newLoc.append(nameDiv);
            newLoc.hover(hoverOverLocation, hoverOutLocation);

            hasLikedLocs = true;
        }

        if (hasLikedLocs === true) {
            $likedLocations.parent( ).removeClass('hide');
        }
    }

    function hoverOverLocation( ) {
        var container = $(this);
        var locationName = container.find('div.LocationName');
        if (locationName.length) {
            $locationNamePopup.text(locationName.text());
            $(document).mousemove(function(e) {
                $locationNamePopup.css({'top': e.pageY + 20, 'left': e.pageX - 10});
            });

            $locationNamePopup.show( );
        }
    }

    function hoverOutLocation( ) {
        $locationNamePopup.hide( );
    }

    function getPastHostEvents( ) {
        attendanceManager.getPastHostedEvents(user.userId, 3, 0, receivePastHostEvents);
    }
    function receivePastHostEvents(response) {
        recieveEvents($('#PastEventsHosted'), response);
    }
    function getUserPastEvents( ) {
        attendanceManager.getPastAttendedEvents(user.userId, 3, 0, receiveUserPastEvents);
    }
    function receiveUserPastEvents(response) {
        recieveEvents($('#PastEventsAttended'), response);
    }

    function recieveEvents($pastEventsAttended, response){
        
        var newEvent;
        var ev;
        var dateOfAttendance;
        var eventLink;
        var hasPastEvents = false;

        for (var i = 0, len = response.pastEvents.length; i < len; i++) {
            ev = response.pastEvents[i];
            dateOfAttendance = new Date(ev.starts_at);
            eventLink = '/location/featured/' + ev.featured_event_id;

            newEvent = $pastEventSkeleton.clone(true);

            newEvent.find('a').text(ev.headline).attr('href', eventLink);
            newEvent.find('button').bind('click', function( ) {
                top.location = eventLink;
            });
            newEvent.find('div.PastEventInfo').html(days[dateOfAttendance.getDay()] + ', '
						     + months[dateOfAttendance.getMonth()] + ' '
						     + dateOfAttendance.getDate() + '<br />'
                    + ev.description);

            if (i + 1 === len) {
                newEvent.addClass('last');
            }

            newEvent.appendTo($pastEventsAttended);
            hasPastEvents = true;
        }

        if (hasPastEvents === true) {
            $pastEventsAttended.parent( ).removeClass('hide');
        }
    }
    /*  function receiveUserPastEvents( response ) {
     var $pastEventsAttended = $( '#PastEventsAttended' );
     var newEvent;
     var ev;
     var dateOfAttendance;
     var eventLink;
     var hasPastEvents = false;
     
     for ( var i = 0, len = response.pastEvents.length; i < len; i++ ){
     ev = response.pastEvents[i];
     dateOfAttendance = new Date(ev.date * 1000);
     eventLink = '/location/' + ev.id + '/' +  dateOfAttendance.getFullYear() + '-' + (dateOfAttendance.getMonth() + 1)+ '-' + dateOfAttendance.getDate(); 
     
     newEvent = $pastEventSkeleton.clone(true); 
     
     newEvent.find('a').text( ev.name ).attr('href', eventLink); 
     newEvent.find('button').bind('click',function( ){
     top.location = eventLink;
     });
     newEvent.find('div.PastEventInfo').html( days[dateOfAttendance.getDay()] + ', '
     + months[dateOfAttendance.getMonth()] + ' '
     + dateOfAttendance.getDate() + '<br />'
     + ev.streetAddress + ', '
     + ev.cityName );
     
     if ( i + 1 === len ){
     newEvent.addClass('last');
     }
     
     newEvent.appendTo( $pastEventsAttended );
     hasPastEvents = true;
     }
     
     if ( hasPastEvents === true ) {
     $pastEventsAttended.parent( ).removeClass('hide');
     }
     }*/

    function showPhoto( ) {
        $(document).unbind('keyup.PhotoPopup').bind('keyup.PhotoPopup', function(e) {
            if (e.keyCode == 27) {
                closePhoto();
            }
        });

        $activePhoto = $(this);
        var markup = '<div id="PhotoPopup">'
                + '<div class="ClosePhotoWindow">&#10006;</div>'
                + '<div class="inner">'
                + (photoCount ? '<a href="#" id="PhotoPopup_Prev">Previous</a>' : '')
                + '<img src="' + $activePhoto.attr('data-large') + '" />'
                + (isViewer() ? '<div class="DeletePhoto">delete</div><div class="MakeDefault">make default</div> ' : '')
                + (photoCount ? '<a href="#" id="PhotoPopup_Next">Next</a>' : '')
                + '</div>'
                + '</div>';

        $popup = $(markup).appendTo('body');
        Main.centerObject($popup);
        $popup.css('top', 50 + $(window).scrollTop() + 'px');
        var $overlay = Main.overlay();
        $overlay.bind('click', closePhoto);
        $('.ClosePhotoWindow').bind('click', closePhoto);

        $('.DeletePhoto').bind('click', function() {
            $.ajax({
                url: '/photoManager/json/deletephoto',
                type: 'POST',
                dataType: 'json',
                data: {
                    photoId: $activePhoto.attr('photo-id'),
                },
                success: function(data) {
                    location.reload(true);
                }
            });
        });

        $('.MakeDefault').bind('click', function() {
            $.ajax({
                url: '/photoManager/json/makedefault',
                type: 'POST',
                dataType: 'json',
                data: {
                    photoId: $activePhoto.attr('photo-id')
                },
                success: function(data) {
                    location.reload(true);

                }
            });
        });

        $('#PhotoPopup_Prev').bind('click', function( ) {
            $activePhoto = $activePhoto.prev();
            if ($activePhoto.is('img') === false) {
                $activePhoto = $photos.filter(':last');
            }
            $popup.find('img').attr('src', $activePhoto.attr('data-large'));
            return false;
        });

        $('#PhotoPopup_Next').bind('click', function( ) {
            $activePhoto = $activePhoto.next();
            if ($activePhoto.is('img') === false) {
                $activePhoto = $photos.filter(':first');
            }
            $popup.find('img').attr('src', $activePhoto.attr('data-large'));
            return false;
        });
    }

    function closePhoto( ) {
        $(document).unbind('keyup.PhotoPopup');
        $popup.remove( );
        $popup = null;
        Main.removeOverlay( );
    }

    init( );
}
