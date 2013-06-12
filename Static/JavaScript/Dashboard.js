var Home = function() {
    var attendanceManager = new AttendanceManager( );
    var activeDate = formatDate(new Date()); 
    var eventDays = $('li','#EventDays').filter(':not(.last)');
    var eventList = $('ul.EventList');
    var eventSkeleton = eventList.find('li.LocationSkeleton').removeClass('LocationSkeleton').remove();
    var events = eventList.find('li');
    var CommentSkeleton = $('.CommentSkeleton').removeClass('CommentSkeleton').remove();
    
    function init() {
	eventDays.click(function( ){
	    selectDate($(this).attr('data-date'), true); 
	});
	selectDate(activeDate, true);
	//loadComments();
    }
    
    
    function loadTopCommentForLocationAndDate( locId ) {
	//console.log('loadTopCommentsForLocationAndDate: '+locId+' '+activeDate);
	Main.fetchFromServer('/network/json/gettopcomments', {location: locId, date: activeDate, limit: 1 }, receiveComment);
    }
    
    function receiveComment( response ) {
	if ( response.comments.length > 0 ) {
	    var comment = newComment(response.comments[0]);
	    // make the comment slide in here
	    //console.log(response.comments[0].locationid);
	    comment.removeClass('hide');
	    $('.Event[data-location-id='+response.comments[0].locationid+']').append(comment);
	    comment.animate({
		bottom: '0'
	    }, 500, function() {
		// Animation complete.
	    });
	}
    }
    function newComment( comment ) {
	var newComment = CommentSkeleton.clone(true);
	newComment.attr('data-comment-id', comment.id);
	newComment.find('span.UserName').text(comment.user.firstName);
	newComment.find('a.UserLink').attr('href', '/profile/' + comment.user.userId);
	newComment.find('img').attr('src','/photo/' + comment.user.userId + '/Small');
	newComment.find('.Comment').html(comment.message);
	newComment.find('.Comment').linkify();
	newComment.find('.Comment').find('a').attr('target','_blank');
	//newComment.removeClass('hide');
	
	return newComment;
    }
    
    function selectDate(selectedDate, showLoading) {
	activeDate = selectedDate; 
	eventDays.removeClass('selected');
	eventDays.filter('[data-date=' + selectedDate +']').addClass('selected');
	events.remove( );
	events = [ ];
	if (showLoading) {
	    $('<li class="Loading"><img src="/Static/Images/General/loading.gif" /><br />Loading...</li>').appendTo(eventList);
	}
	loadEventsForDate(selectedDate);
    }
    
    function loadEventsForDate(selectedDate) {
	Main.fetchFromServer('/locations/json/forDate', { date: selectedDate, limit: 18 }, receiveEventsForDate);
    }
    
    function receiveEventsForDate(response) {
	for ( var i = 0, len = response.locations.length; i < len; i++ ) {
	    newEvent(response.locations[i]);
	}
	
	eventList.find('li.Loading').remove();
	events = eventList.find('li');
	eventList.css('opacity','0');
	events.removeClass('hide');
	eventList.animate({
	    opacity: 1
	}, 1500, function() {} );
	
	for ( var i = 0, len = response.locations.length; i < len; i++ ) {
	    loadTopCommentForLocationAndDate( response.locations[i].id );
	}
    }
    
    function newEvent(locationInfo) {
	var newLocation = eventSkeleton.clone(true);
	
	var locationImage = newLocation.find('.LocationImage');
	locationImage.prepend('<img src="/Photos/Locations/'+locationInfo.image+'" />');
	
	locationImage.bind('click', function(){
            top.location = '/location/' + locationInfo.id + '/' + activeDate; 
	});

	newLocation.find('.Event').attr('data-location-id', locationInfo.id);
	newLocation.find('.EventTitle').html(locationInfo.name);
	newLocation.find('.EventLocation').text(locationInfo.streetAddress);
	
	newLocation.hover(
	    function() {
		$(this).find('.TitleLocationContainer').animate({
		    opacity: 0.9,
		    top: '-4px'
		}, 150, function() {
		    // Animation complete.
		});
		$(this).find('.CommentContainer').animate({
		    opacity: 0.9,
		    bottom: '-6px'
		}, 150, function() {
		    // Animation complete.
		});
	    },
	    function() {
		$(this).find('.TitleLocationContainer').animate({
		    opacity: 1,
		    top: '0'
		}, 150, function() {
		    // Animation complete.
		});
		$(this).find('.CommentContainer').animate({
		    opacity: 1,
		    bottom: '0'
		}, 150, function() {
		    // Animation complete.
		});
	    });

	if ( locationInfo.userStatus ) {
	    if ( locationInfo.userStatus == 'yes' ) {
		newLocation.find('.AttendingCount').addClass('active');
	    }
	}
	
	var buttons = newLocation.find('.AttendingCount');
	buttons.click(function(){
	    if($(this).hasClass('active')) {
		var attendingCountContainer = $(this).find('strong');
		attendingCountContainer.text(parseInt(attendingCountContainer.text(), 10) - 1);
		attendanceManager.setAttendanceStatus(locationInfo.id, activeDate, 'no');
		$(this).removeClass('active');
	    }
	    else {
		var attendingCountContainer = $(this).find('strong');
		attendingCountContainer.text(parseInt(attendingCountContainer.text(), 10) + 1);
		attendanceManager.setAttendanceStatus(locationInfo.id, activeDate, 'yes');
		$(this).addClass('active');
	    }
        });
	
	var attendingCounts = newLocation.find('ul.AttendingCounts');
	attendingCounts.find('li.AttendingCount').html('<strong>' + locationInfo.attendanceCounts.yes + '</strong> Going');	
	newLocation.appendTo(eventList);
    }
        
    function hoverOverMember( ) {
	var container = $(this);
	var memberName = container.find('div.MemberName');
	if ( memberName.length ) {
	    memberName.show( );
	}
	else {
	    var nameDiv = $('<div class="MemberName"></div>');
	    nameDiv.hide().addClass('mouseOver');
	    container.append(nameDiv);
	    
	    Main.fetchFromServer('/user/' + $(this).attr('data-user-id'), 
				 { fields: 'firstName,lastName' }, 
				 function( response ){
				     nameDiv.text(response.firstName + ' ' + response.lastName);
				     nameDiv.css('left', -1 * ( nameDiv.outerWidth( ) - container.outerWidth( ) ) / 2 );
				     if(nameDiv.hasClass('mouseOver')) {
					 nameDiv.show();
				     }
				 } );
	}
    }
    
    function hoverOutMember( ) {
	$(this).find('div.MemberName').hide( ).removeClass('mouseOver');
    }
    
    function formatDate(dateObject) {
	return dateObject.getFullYear() + '-' + (dateObject.getMonth() + 1) + '-' + dateObject.getDate();
    }
    
    init();
};

$(function(){
    new Home( );
});
