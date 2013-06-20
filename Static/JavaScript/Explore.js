var LocationsMain = function(loc){    
    var EVENTS_PER_PAGE = 6;

    var _this = this;
    var $locationList = $('#LocationList');

    var attendanceManager = new AttendanceManager();
    var $pastEventSkeleton = $( '#PastEventSkeleton' ).removeAttr( 'id' ).remove();
    var days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
    var months = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
    var opacitySupport = $.support.opacity;
    
    function init( ) {
	for (tag in Viewer.tags) {
    	    var str = Viewer.tags[tag]
    	    $('div[data-tag-desc=\"'+str+'\"]').addClass('active');
	}
	if(typeof initlocs === "undefined") {
	    getLocations(1);
	}
	else {
	    receiveLocations(initlocs, 0);
	}
	//_this.drawMap( );
	//getUserPastEvents( );
	$('.tagbutton').bind('click', function(){
    	    var tagdesc = $(this).attr('data-tag-desc');
    	    showLoadingOverlay();
    	    if($(this).hasClass('active')) {
    		$.ajax( {
  		    url: '/network/json/deltag',
  		    type: 'GET',
  		    data: { userId: Viewer.userId, tags: tagdesc },
  		    success: function( data ) {
  		        getLocations(1);
  		    }
  		} );
    		$(this).removeClass('active');
    		//getActiveFilters();
    	    }
    	    else {
    		$.ajax( {
		    url: '/network/json/addtag',
		    type: 'GET',
		    data: { userId: Viewer.userId, tags: tagdesc },
		    success: function( data ) {
		        getLocations(1);
		    }
    		} );
    		$(this).toggleClass('active');
    		//getActiveFilters();
    	    }
    	    
	});
	
    }
    
    function showLoadingOverlay() {
	var loadingpopup ='<li class="Loading"><br />Loading...<br /><br /><img src="/Static/Images/General/loading.gif" /></li>';
	$locationList.prepend(loadingpopup);
	$('.Loading').height($locationList.height());
    }
    
    this.drawMap = function( ) {
	geocoder.geocode( { 'address': '301 Race St Philadelphia, PA' }, function( results, status ){
	    if ( status == google.maps.GeocoderStatus.OK ) {
		map.setCenter( results[0].geometry.location );
	    }
	} );
	
	var options = {
	    zoom: 12,
	    mapTypeId: google.maps.MapTypeId.ROADMAP,
	    streetViewControl: false,
	    mapTypeControl: false
	};
	
	map = new google.maps.Map( document.getElementById("Map"), options );
	//getLocations(1);
    };
    
    function getLocations(page) {

	if ( page == 1 && supports_html5_storage() ) {
	    var afilters = getActiveFilters().toString();
	    var cachedlocs = localStorage[afilters];
	    if (cachedlocs != null) {
		receiveLocations($.parseJSON(cachedlocs), 1);
	    }
	    else {
		Main.fetchFromServer(
		    '/locations/json/popular', 
		    {limit: EVENTS_PER_PAGE, offset: ((page-1)*EVENTS_PER_PAGE) }, 
		    function(response){
			localStorage[afilters] = JSON.stringify(response);
			receiveLocations(response, page);
		    }
		);
	    }
	}
	else {
	    Main.fetchFromServer('/locations/json/popular', 
                    {limit: EVENTS_PER_PAGE, offset: ((page-1)*EVENTS_PER_PAGE) },
                    function(response){
                        receiveLocations(response, page);
                    });
	}
    }
    
    function clearMarkers( ) {
	for ( var i = 0, len = markers.length; i < len; i++ ) {
	    markers[i].setMap(null);
	}
    }
    
    function receiveLocations(response, page) {
	$locationList.empty();
	
	for ( var i = 0; i < response.locations.length; i++ ) {
	    $locationList.append(getNewLocationItem(response.locations[i]));
	    var dateOfAttendance = new Date(response.locations[i].date * 1000);
	    var dateString = dateOfAttendance.getFullYear() + '-' + (dateOfAttendance.getMonth() + 1)+ '-' + dateOfAttendance.getDate();
	    var link = '<h2><a target="_blank" href="/location/'+response.locations[i].id+'/'+dateString+'">'+response.locations[i].name+'</a></h2>'
	}
	
	setPaging(page, Math.ceil(response.count / 4));
    }
    
    function setPaging(currentPage, totalPages) {
	currentPage = parseInt(currentPage);
	totalPages = parseInt(totalPages);
	
	var startPage = 1;
	if ( currentPage == 2) {
	    startPage = 1;
	}
	else if ( currentPage > 2 ) {
	    startPage = currentPage - 2;
	}
	
	var lastPage = Math.min(currentPage + 3, totalPages);
	
	var paging = $("#LocationListPaging");
	paging.empty();
	paging.append('<li>Page:</li>');
	
	for ( var i = startPage; i <= lastPage; i++ ) {
	    paging.append('<li class="page ' + ( i === currentPage ? 'selected' : '' ) + '"><a href="#">' + i + '</a></li>');
	}
	
	paging.append('<li class="clear"></li>');
	
	paging.find('a').bind('click', function( ){
	    showLoadingOverlay();
	    getLocations($(this).text());
	    return false;
	});
    }
    
    function getActiveFilters() {
	var filters = new Array();
	$('.tagbutton.active').each(
	    function () {
		filters[filters.length] = $(this).attr('data-tag-desc');
	    }
	);
	return filters;
    }
    
    function oc(a){
	var o = {};
	for(i in a) {
	    o[a[i]]='';
	}
	return o;
    }
    
    function getNewLocationItem(loc) {
	//var dateOfAttendance = new Date(loc.date * 1000);
	var dateOfAttendance = new Date();
	var dateString = dateOfAttendance.getFullYear() + '-' + (dateOfAttendance.getMonth() + 1)+ '-' + dateOfAttendance.getDate();
	var tags = '';
	var activefilters = getActiveFilters();
	var tagobj = oc(loc.tags);
	
	
	for (filter in activefilters) {
    	    if ( activefilters[filter] in tagobj )
    		tags += '<span class="LocationTag">'+activefilters[filter] + '</span>' ;
	}

	var markup = '<li class="loc">'
    	    +   '<img class="LocationPicture" src="/Photos/Locations/'+loc.image+'" />'
	    +   '<div class="TitleLocationContainer">'
	    +     '<div class="EventTitle">'+loc.name+'</div>'
	    +     '<div class="EventLocation">' + loc.streetAddress + '</div>'
	    +   '</div>'
            +   '<div class="ResponsesAndDate">'
            +	 '<div class="AttendingList">'
            +		'<div class="LikeCount">'
            +		'<strong>' + loc.likes + '</strong>'
            +		'Likes'
            +		'</div>'
            +		'<div class="LikeButton">'
            +		'Like'
            +		'</div>'
            +	 '</div>'
            +   '<div class="LocationTags">' + tags + '</div>'
            +   '</div>'
            +   '<div class="LocationName">' 
            +     '<a href="/location/' + loc.id + '/' + dateString + '">'
            +       loc.name 
            +     '</a>'
            +   '</div>'
            +   '<div class="LocationDescription"><div class="scroll">' + loc.description + '</div></div>'
            +   '<div style="clear:both"></div>'
            + '</li>';
	
	var $obj = $(markup);
	
	$obj.find('.LikeButton').bind('click', function(){
    	    if($(this).hasClass('active')) {
    		$.ajax( {
  		    url: '/network/json/unlikelocation',
  		    type: 'GET',
  		    data: { locationId: loc.id },
  		    success: function( data ) {  },
  		    error: function(data) { console.log(data.e);}
  		} );
    		$obj.find('.LikeCount').html('<strong>'+(--loc.likes)+'</strong>Likes');
    		$(this).removeClass('active');
		updateLocalStorage(loc.id, -1);
    	    }
    	    else {
    		$.ajax( {
		    url: '/network/json/likelocation',
		    type: 'GET',
		    data: { locationId: loc.id },
		    success: function( data ){  },
  		    error: function(data) { console.log(data.e); }
    		} );
    		$obj.find('.LikeCount').html('<strong>'+(++loc.likes)+'</strong>Likes');
    		$(this).addClass('active');
		updateLocalStorage(loc.id, 1);
    	    }
	});
	
	if(loc.likedByUser) $obj.find('.LikeButton').addClass('active');
	
	$obj.find('div.Responses_Yes, div.Responses_Maybe').bind('click', function(){
	    attendanceManager.setAttendanceStatus( loc.id, dateString, $(this).attr('data-status'), function( response ){ attendanceResponse( $obj, response ); } )
	});
	
	$obj.find('.LocationPicture').bind('click', function(){
            top.location = '/location/' + loc.id + '/' + dateString; 
	});
	

	$obj.hover(
	    function() {
		$('.scroll').stop();
		var scr = $(this).find('.scroll');

		if(opacitySupport) {
		    $(this).find('.TitleLocationContainer').animate({
			opacity: 0.9,
			top: '-3px'
		    }, 150);
		    scr.animate({opacity: 0.95}, 150);
		}
		else {
		    $(this).find('.TitleLocationContainer').animate({
			top: '-3px'
		    }, 150);
		}
		scr.animate({top: '-'+(scr.height()-35)+'px'}, (scr.height()*70), 'linear');
	    },
	    function() {
		$('.scroll').stop();
		var scr = $(this).find('.scroll');
		var locTopBar = $(this).find('.TitleLocationContainer');
		if(opacitySupport) {
		    locTopBar.animate({
			opacity:1,
			top: '0'
		    }, 150);
		    scr.animate({opacity: 1}, 150);
		}
		else {
		    locTopBar.animate({top: '0'}, 150);
		}
		scr.animate({top: '5px'}, 300);
	    });

	return $obj;
    }
    
    function updateLocalStorage(locid, amount) {
	// We could potentially update every location in localstorage when someone likes/dislikes a place,
	// but that's crazy inefficient. So, we'll just clear the storage and let it repopulate it again.
	// Using localstorage is still good for us since we'll save a bunch of db hits when people don't change their likes frequently.
	if (supports_html5_storage()) {
	    localStorage.clear();
	}
    }

    function attendanceResponse($container, response) {
	$container.find('div.Responses_Yes').text('Yes: ' + response.yes);
	$container.find('div.Responses_Maybe').text('Maybe: ' + response.maybe);
    }
    
    function getUserPastEvents( ) {
	attendanceManager.getPastAttendedEvents( Viewer.userId, 5, 0, receiveUserPastEvents ); 
    }
    
    function receiveUserPastEvents( response ) {
	var $pastEventsAttended = $( '#PastEventsAttended' );
	var newEvent;
	var ev;
	var dateOfAttendance;
	var eventLink;
	
	for ( var i = 0, len = response.pastEvents.length; i < len; i++ ) {
	    ev = response.pastEvents[i];
	    dateOfAttendance = new Date(ev.date * 1000);
	    eventLink = '/location/' + ev.id + '/' +  dateOfAttendance.getFullYear() + '-' + (dateOfAttendance.getMonth() + 1)+ '-' + dateOfAttendance.getDate(); 
	    
	    newEvent = $pastEventSkeleton.clone(true); 
	    
	    newEvent.find('a').text( ev.name ).attr('href', eventLink); 
	    /*newEvent.find('button').bind('click',function( ){
              top.location = eventLink;
	      });*/
	    newEvent.find('div.PastEventInfo').html( days[dateOfAttendance.getDay()] + ', '
						     + months[dateOfAttendance.getMonth()] + ' '
						     + dateOfAttendance.getDate() + '<br />'
						     + ev.streetAddress + ', '
						     + ev.cityName );
	    
	    newEvent.appendTo( $pastEventsAttended );
	}
    }

    function supports_html5_storage() {
	try { return 'localStorage' in window && window['localStorage'] !== null; } 
	catch (e) { return false; }
    }

    init( );
}

$(function( ){
    var lm = new LocationsMain();
} );
