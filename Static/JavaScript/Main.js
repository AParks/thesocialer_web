var Main = {
    GENDER_MALE: 'male',
    GENDER_FEMALE: 'female',
    
    fetchFromServer : function( url, params, callback ) {
	$.ajax( {
	    url: url,
	    type: 'GET',
	    data: params,
	    success: function( data ) {
		if ( typeof callback === 'function' ) {
		    callback( data );
		}
	    },
	    error:function (data){
    		//alert(data.responseText);
	    }  
	} );
    },
    
    formatDate: function(dateObject) {
	return dateObject.getFullYear() + '-' + (dateObject.getMonth() + 1) + '-' + dateObject.getDate();
    },
    
    centerObject: function($obj) {
	$obj.css({position: 'absolute',
		  top: (($(window).height() - $obj.outerHeight()) / 2) + $(window).scrollTop() + 'px',
		  left: (($(window).width() - $obj.outerWidth()) / 2) + $(window).scrollLeft() + 'px'});
    },
    
    overlay: function( ) {
	return $('<div id="overlay"></div>').appendTo('body');
    },
    
    removeOverlay: function( ) {
	$('#overlay').remove( );
    }
};
