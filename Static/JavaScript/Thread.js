var Thread = function( ){
  var $input = $('#Response');
  var $sendButton = $('#RespondButton');

    function init( ) {
	$input.bind('keyup', checkInput);
	$sendButton.bind('click', send);
    }
    
    function send( ) {
	var message = $input.val();
	if ( message === '' ) {
	    return;
	}
	MessageSender.sendMessage( { userId: $input.attr('recipient') }, message, sendCallback)
    }
    
    function sendCallback( response ) {
	var $result;
	if ( response.success === true ) {
	    $result = $('<span class="SendResult">Message sent!</span>');
	    location.reload(true);
	}
	else {
	    $result = $('<span class="SendResult">Failed to send message. Try again later.</span>');
	}
	
	$sendButton.after($result);
	$result.delay(3000).slideUp();
    }
    
    function checkInput( ) {
	if ( $sendButton.hasClass('Gray') && $input.val( ) !== '' ) {
	    $sendButton.addClass('Blue').removeClass('Gray');
	}
	else if ( $sendButton.hasClass('Blue') && $input.val( ) === '' ) {
	    $sendButton.removeClass('Blue').addClass('Gray');
	}
    }
    
    init( );
};

new Thread();
