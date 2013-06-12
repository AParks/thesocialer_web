var Settings = function(){    
    var opacitySupport = $.support.opacity;
    
    function init( ) {
	$("#SettingsTabs li").bind('click',function() {
	    var tab = $(this);
	    if(!tab.hasClass("active")) {
		$('#SettingsTabs li.active').removeClass('active');
		tab.addClass("active");
		$('.SettingsContent.active').removeClass('active');
		$('#'+tab.text()).addClass('active');
	    }
	});
	$('.EditButton').toggle(
	    function() {
		$(this).text('cancel');
		$(this).parent().parent().find('input, select').attr('disabled',false);
	    },
	    function(){
		// resets values to what they started at
		$(this).text('edit');
		var par = $(this).parent().parent();
		par.find('input, select').attr('disabled',true);
		par.find('option[disabled]').attr('selected','selected');
		par.find('input').val(par.find('input').attr('data-default'));
	    }
	);
	
	var genvalidator = $("#GeneralSettings").validate({ 
            rules: {
		email: { email: true },
            },
            messages: { 
		email: { 
                    minlength: "Enter a valid email",
		    email: "Enter a valid email"
		}
            }, 
            errorPlacement: function(error, element) {
		if (element.attr("name") == "month" || element.attr("name") == "day" || element.attr("name") == "year") {
		    error.appendTo( element.parent().parent().parent().next() );
		}
		else if ( element.is(":radio") ) {
                    error.appendTo( element.parent().parent().parent().next() ); 
		}
		else if ( element.is(":checkbox") ) {
                    error.appendTo ( element.next() );
		} 
		else {
                    error.appendTo( element.parent().next().next() ); 
		}
            }, 
            submitHandler: function() {
		saveGeneral();
            }, 
            success: function(label) { label.remove(); } 
	});
	var passvalidator = $("#PasswordSettings").validate({ 
            rules: { 
		curpass: { required: true }, 
		newpass: { minlength: 5 }, 
		confirmpass: {
                    equalTo: "#newpass" 
		}
            },
            messages: { 
		newpass: { 
                    minlength: jQuery.format("Enter at least {0} characters") 
		}, 
		confirmpass: { 
                    equalTo: "Enter the same password as above" 
		}
            }, 
            errorPlacement: function(error, element) {
                error.appendTo( element.parent().next() ); 
            }, 
            submitHandler: function() {
		savePassword();
            }, 
            success: function(label) { label.remove(); } 
	});

    }    

    function saveGeneral( ) {
	var params = { };
	params["FirstName"] = $('#fname').val();
	params["LastName"] = $('#lname').val();
	params["Email"] = $('#email').val();
	var dob = $('#year').val()+"-"+$('#month').val()+"-"+$('#day').val();
	params["DOB"] = dob;
	$.ajax( {
	    url: '/profile/json/save',
	    type: 'POST',
	    dataType: 'json',
	    data: params, 
	    success: function( data ) {
		var msg = 'Profile saved!';
		if ( data.success === false ) {
		    msg = 'Error saving profile.  Please try again later.';
		}
		$('#GeneralSettings').find('input, select').attr('disabled',true);
		$('.EditButton').text('edit');
		$('.SaveStatus').text(msg);
	    }
	} );
    }
    function savePassword( ) {
	var curpass = new jsSHA( $('#curpass').val( ) , "ASCII");
	var hashcp = curpass.getHash("SHA-256","HEX");

	var newpass = new jsSHA( $('#newpass').val( ) , "ASCII");
	var hashnp = newpass.getHash("SHA-256","HEX");
	var params = { };
	params["OldPassword"] = hashcp;
	params["NewPassword"] = hashnp;
	$.ajax( {
	    url: '/profile/json/pass',
	    type: 'POST',
	    dataType: 'json',
	    data: params, 
	    success: function( data ) {
		var msg = 'Password changed!';
		if ( data.result === false ) {
		    msg = 'Incorrect password.';
		}
		else {
		    $('#PasswordSettings').find('input').val('');
		}
		$('#PasswordSettings .SaveStatus').text(msg);
	    }
	} );
    }

    init( );
}

$(function( ){
    var set = new Settings();
} );
