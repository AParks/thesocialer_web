var AttendanceManager = function( ){
    this.setAttendanceStatus = function( locationId, date, status, callback ) {
	$.ajax( {
	    url: '/locations/json/setAttendance',
	    type: 'GET',
	    data: { location: locationId, status: status, date: date },
	    success: function( data ) {

		if ( typeof callback === 'function' ) {
		    callback( data.result );
		}
	    }
	} );
    };
    
    this.setFeaturedEventAttendanceStatus = function( eventId, status, callback ) {
	$.ajax( {
	    url: '/locations/json/setAttendance',
	    type: 'GET',
	    data: { eventId: eventId, status: status },
	    success: function( data ) {
		if ( typeof callback === 'function' ) {
		    callback( data.result );
		}
	    }
	} );
    };
    
    this.getPastAttendedEvents = function( userId, limit, offset, callback ) {
	Main.fetchFromServer('/locations/json/pastEvents', { userId: userId, limit: limit, offset: offset }, callback);
    };
}
