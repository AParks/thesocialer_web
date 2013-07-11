var _MessageSender = function( ){
    var _this = this;
    var $popup;
    
    this.showPopup = function( userData ){
	dismiss( );
	
	var markup = '<div id="SendMessagePopup">'
            +   '<div class="inner">'
            +     'Send ' + userData.firstName + ' a message<br />'
            +     '<textarea />'
            +     '<div class="ButtonContainer">'
            +       '<button class="Gray standard">Cancel</button>'
            +       '<button class="Blue standard">Send</button>'
            +     '</div>'
            +   '</div>'
            + '</div>';
	
	$popup = $(markup).appendTo('body');
	$popup.find('button:first').bind('click', dismiss);
	$popup.find('button:last').bind('click', function( ){ _this.sendMessage( userData, $popup.find('textarea').val(), sendMessageCallback); } );
	Main.centerObject( $popup );
    };
    
    function dismiss( ) {
	if ( $popup ) {
	    $popup.remove( );
	    delete $popup;
	}
    }
    
    this.sendMessage = function(userData, message, callback) {
	Main.fetchFromServer('/messages/json/send', { to: userData.userId, message: message}, callback);
    }
    
    function sendMessageCallback(response) {
	var markup = '';
        
        var resp = $.parseJSON(response);
	if ( resp.success ) {
	    markup = 'Message sent!<br /><button class="Blue standard">OK</button>';  
	}
	else {
	    markup = 'Error sending message.  Try again later.';
	}
	
	$popup.find('div.inner').html( markup );
	$popup.addClass('finished');
	$popup.find('button').bind('click', dismiss);
    }
}

MessageSender = new _MessageSender( );
