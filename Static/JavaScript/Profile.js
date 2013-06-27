var Profile = function( user ) {
    var attendanceManager = new AttendanceManager();
    var $pastEventSkeleton = $( '#PastEventSkeleton' ).removeAttr( 'id' ).remove();
    var $likedLocationSkeleton = $( '#LikedLocationSkeleton' ).removeAttr( 'id' ).remove();
    var $locationNamePopup = $('#LocationNamePopup');
    var days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
    var months = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
    
    var $photos = $('img', 'div.PhotoThumbs');
    var photoCount = $photos.length;
    var $popup;
    var $activePhoto;
    
    function init( ) {
	if ( isViewer( ) ) {
	    initEditProfile( );
	}
	else {
    	    $('#FriendRequest').bind('click', function( ){ FriendRequestManager.showPopup({name: user.firstName, userId: user.userId}); });
	}
	
	getUserPastEvents();
	getUserLikedLocations();
	$photos.bind('click', showPhoto);  
	$('#SendMessage').bind('click', showSendMessage);
    }
    
    function showSendMessage( ) {
	MessageSender.showPopup(user);
	return false;
    }
    
    function initEditProfile( ) {
	var $editPencil = $('<img class="editImage" src="/Static/Images/editPencil.png" />');
	var $focusPencil = $editPencil.clone();
	
	$( 'div.value' ).hover(
	    function( ) {
		$(this).addClass( 'edit' );
		$editPencil.appendTo($(this).parent());
	    },
	    function( ) {
		if ( $(this).find('input:focus').length === 0 ) {
		    $(this).removeClass('edit');
		}
		$editPencil.remove();
	    }
	);
	$('div.value input').bind('focus', function( ){
	    var $par = $(this).parent();
	    $par.addClass( 'edit' );
	    $focusPencil.appendTo($par.parent());
	    $('#SaveProfile').slideDown(100);
	}).bind('blur', function( ){
	    var $par = $(this).parent();
	    $par.removeClass( 'edit' );
	    $focusPencil.remove();
	    $editPencil.remove();
	});
	
	$('#SaveProfile').bind('click', function( ){
	    save( );
	});
	
	$('#UploadPhotoLink').bind('click', function( ){
	    var pu = new PhotoUploader();
	    pu.start();
	    return false;
	});
    }
    
    function save( ) {
	$('#SaveProfile').slideUp(100);
	
	var params = { };
	$('div.UserInfo').find('input').each(function( ){
	    var $t = $(this);
	    params[$t.attr('data-field')] = $t.val();
	});
	
	$.ajax( {
	    url: '/profile/json/save',
	    type: 'POST',
	    dataType: 'json',
	    data: params, 
	    success: function( data ) {
		var msg = '<div>Profile saved!</div>';
		if ( data.success === false ) {
		    msg = '<div>Error saving profile.  Please try again later.</div>';
		}
		
		$(msg).addClass('ProfileSaveMessage').appendTo($('div.UserInfo')).delay(3000).slideUp(function(){$(this).remove();});
	    }
	} );
    }
    
    function isViewer( ) {
	return Viewer.userId === user.userId;
    }
    
    function getUserLikedLocations() {
	Main.fetchFromServer('/locations/json/likedLocations', { userId: user.userId }, receiveUserLikedLocations);
    }
    
    function receiveUserLikedLocations( response ) {
	var $likedLocations = $( '#LikedLocations' );
	var newLoc;
	var loc;
	var locLink;
	var hasLikedLocs = false;
	
	for ( var i = 0, len = response.likedLocations.length; i < len; i++ ) {
	    loc = response.likedLocations[i];
	    locLink = '/location/' + loc.id + '/'; 
	    
	    newLoc = $likedLocationSkeleton.clone(true); 
	    
	    newLoc.find('img').attr('src', '/Photos/Locations/'+loc.image); 
	    newLoc.find('a').attr('href', locLink); 
	    
	    if ( i + 1 === len ) {
    		newLoc.addClass('last');
	    }
	    
	    newLoc.appendTo( $likedLocations );
	    
	    
	    var nameDiv = $('<div class="LocationName"></div>');
	    nameDiv.hide().addClass('mouseOver');
	    nameDiv.text(loc.name);
	    nameDiv.css('left', -1 * ( nameDiv.outerWidth( ) - newLoc.outerWidth( ) ) / 2 );
	    newLoc.append(nameDiv);
	    newLoc.hover(hoverOverLocation, hoverOutLocation);
	    
	    hasLikedLocs = true;
	}
	
	if ( hasLikedLocs === true ) {
    	    $likedLocations.parent( ).removeClass('hide');
	}
    }
    
    function hoverOverLocation( ) {
	var container = $(this);
	var locationName = container.find('div.LocationName');
	if ( locationName.length ) {
    	    $locationNamePopup.text(locationName.text());
    	    $(document).mousemove( function(e) {
    		$locationNamePopup.css({'top':e.pageY+20,'left':e.pageX-10});
    	    }); 
    	    
    	    $locationNamePopup.show( );
	}
    }

    function hoverOutLocation( ) {
	$locationNamePopup.hide( );
    }
    
    function getUserPastEvents( ) {
	attendanceManager.getPastAttendedEvents( user.userId, 3, 0, receiveUserPastEvents ); 
    }
    
    function receiveUserPastEvents( response ) {
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
    }
    
    function showPhoto( ) {
	$(document).unbind('keyup.PhotoPopup').bind('keyup.PhotoPopup', function(e){
	    if (e.keyCode == 27){
		closePhoto();
	    }
	});
	
	$activePhoto = $(this);
	var markup = '<div id="PhotoPopup">'
	    +   '<div class="ClosePhotoWindow">&#10006;</div>'
            +   '<div class="inner">'
            +   ( photoCount ? '<a href="#" id="PhotoPopup_Prev">Previous</a>' : '' )
            +   '<img src="' + $activePhoto.attr('data-large') + '" />'
            +	( isViewer() ? '<div class="DeletePhoto">delete</div><div class="MakeDefault">make default</div> ' : '')
            +   ( photoCount ? '<a href="#" id="PhotoPopup_Next">Next</a>' : '' )
            +   '</div>'
            + '</div>';
	
	$popup = $(markup).appendTo('body');
	Main.centerObject($popup);
	$popup.css( 'top', 50 +  $(window).scrollTop() + 'px' );
	var $overlay = Main.overlay();
	$overlay.bind('click', closePhoto);
	$('.ClosePhotoWindow').bind('click', closePhoto);
	
	$('.DeletePhoto').bind('click', function() {
    	    $.ajax( {
    		url: '/photoManager/json/deletephoto',
    		type: 'POST',
    		dataType: 'json',
    		data: { 
    	    	    photoId: $activePhoto.attr('photo-id'),
    		}, 
    		success: function( data ) {
    	    	    location.reload(true);
    		}
    	    });
	});
	
	$('.MakeDefault').bind('click', function() {
    	    $.ajax( {
    		url: '/photoManager/json/makedefault',
    		type: 'POST',
    		dataType: 'json',
    		data: { 
    	    	    photoId: $activePhoto.attr('photo-id')
    		}, 
    		success: function( data ) {
                    location.reload(true);

    		}
    	    });
	});
	
	$( '#PhotoPopup_Prev' ).bind('click', function( ) {
	    $activePhoto = $activePhoto.prev();
	    if ( $activePhoto.is('img') === false ) {
		$activePhoto = $photos.filter(':last');
	    }
	    $popup.find('img').attr('src', $activePhoto.attr('data-large'));
	    return false;
	});
	
	$( '#PhotoPopup_Next' ).bind('click', function( ) {
	    $activePhoto = $activePhoto.next();
	    if ( $activePhoto.is('img') === false ) {
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
