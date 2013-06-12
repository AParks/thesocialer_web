var ResetPassword = function(){    
    
    function init( ) {
	var passvalidator = $("#ResetPasswordForm").validate({ 
            rules: { 
		newpass: { minlength: 5 }
            },
            messages: { 
		newpass: { 
                    minlength: jQuery.format("Enter at least {0} characters") 
		}
            }, 
            errorPlacement: function(error, element) {
                element.parent().append(error  ); 
            }, 
            submitHandler: function() {
		resetPassword();
            }, 
            success: function(label) { label.remove(); } 
	});

    }    

    function resetPassword( ) {
	var newpass = new jsSHA( $('#newpass').val( ) , "ASCII");
	var hashnp = newpass.getHash("SHA-256","HEX");
	var params = { };
	params["ResetCode"] = resetcode;
	params["NewPassword"] = hashnp;
	$.ajax( {
	    url: '/reset/json/reset',
	    type: 'POST',
	    dataType: 'json',
	    data: params, 
	    success: function( data ) {
		var msg = 'Password changed! Feel free to login with your new password at the right.';
		if ( data.result === false ) {
		    msg = 'Something went wrong. Please try using forgot your password again.';
		}
		else {
		    $('#ResetPasswordForm').remove();
		}
		$('#ResetPassContainer').text(msg);
	    }
	} );
    }
    
    init( );
}

$(function( ){
    var reset = new ResetPassword();
} );
