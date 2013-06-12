var Header = function( ) {
};

Header.showFailedLogin = function( ) {
    $('#QuickLoginFormLoginFailed').show( );
    $('#QuickLoginForm').show( );
}

$(function() {

 $('div.fb-login').click(function() {

        FB.login(function(response) {
            // handle the response
            if (response.authResponse) {
                console.log(response);
            } else {
                console.log('You cancelled login or you did not fully authorize the app.');
            }
        }, {scope: 'email,user_birthday, user_education_history, user_location'});

    });
    
    
    $('#HelpButton').bind('click', function() {
        $('.HowItWorks').toggle();
    });

    $('.HowItWorks').find('.CloseWindow').bind('click', function() {
        $(this).parent().hide();
    });

   
   //make "Keep me logged in" checkbox checked by default
//   $('#rememberme')




    $('#ForgotLink').click(
            function() {
                $('#QuickLoginFormLoginFailed').hide();
                $('#break').hide();
                $('#QuickLoginForm').slideUp();
                $('#ForgotPasswordForm').show("slow");
            }
    );

    $('input[type=text][id=college]').css('width', '55%');


    //login modal
    $('#myModal').on('hidden', function() {
        $('div[id=tryagain]').remove();
    }).on('show', function() {
        $('#ForgotPasswordForm').hide();
        $('#QuickLoginForm').show();
        $('#myModal2').modal('hide');
    }).on('shown', function() {
        $('input[type=email]').focus();
    });

    //sign up modal
    $('#myModal2').on('hidden', function() {
        $('label[class=error]').remove();
    }).on('show', function() {
        $('#myModal').modal('hide');
    }).on('shown', function() {
        $('input[id=firstname]').focus();
    });

    //show the password if checkbox is checked
    var passcheck = $('.passcheck');
    passcheck.click(function() {
        var password = $(this).closest('form').find('#password');
        var type = 'password';
        if ($(this).is(':checked'))
            type = 'text';

        password.clone().attr('type', type).insertAfter(password)
                .prev().remove();
    });


    var validator = $("#registration").validate({
        rules: {
            firstname: {
                required: true,
            },
            lastname: {
                required: true,
            },
            password: {
                required: true,
                minlength: 5
            },
            email: {
                required: true,
                email: true
                        // TODO implement this remote stuff
                        //remote: "emails.php" 
            },
            month: {
                required: true
            },
            day: {
                required: true,
                minlength: 1,
                maxlength: 2,
                range: [1, 31],
                number: true
            },
            year: {
                required: true
            },
            gender: {
                required: true
            },
            location: {
                required: true
            }
        },
        messages: {
            firstname: {
                required: "Enter your first name",
            },
            lastname: {
                required: " Enter your last name",
            },
            password: {
                required: "Provide a password",
                rangelength: jQuery.format("Enter at least {0} characters")
            },
            email: {
                email: " Enter a valid email",
                required: " Enter a valid email",
                minlength: " Enter a valid email",
                remote: jQuery.format("{0} is already in use")
            },
            month: {
                required: " Enter your birthdate"
            },
            day: {
                required: " Enter your birthdate"
            },
            year: {
                required: " Enter your birthdate"
            },
            gender: {
                required: " Please select a gender"
            }
        },
        errorPlacement: function(error, element) {
            if (element.attr("name") == "month" || element.attr("name") == "day" || element.attr("name") == "year") {
                if ($('label:contains(birthdate)').length == 0) {
                    element.parent().parent().append(error);
                }
            }
            else if (element.is(":radio")) {
                element.parent().parent().append(error);
            }
            else if (element.attr("id") == "password") {
                error.insertAfter(element.next().next());
            }
            else if (element.attr("id") === "firstname" || element.attr("id") === "lastname") {
                error.insertAfter(element.parent());
            }
            else {
                error.insertAfter(element);
            }
        },
        submitHandler: function() {
            var shaObj = new jsSHA($('#password').val( ), "ASCII");
            var hash = shaObj.getHash("SHA-256", "HEX");
            $.ajax({
                url: '/register',
                type: 'POST',
                data: {
                    firstName: $('#firstname').val(),
                    lastName: $('#lastname').val(),
                    emailAddress: $('#email').val( ),
                    year: $('#year').val( ),
                    month: $('#month').val( ),
                    day: $('#day').val( ),
                    gender: $('input:radio[name=gender]:checked').val(),
                    location: $('input:radio[name=location]:checked').val(),
                    college: $('input[name=college]').val(),
                    password: hash
                },
                success: function(data) {
                    location.reload(true);
//                    top.location = '/trending';
                }
            });
        },
        // set this class to error-labels to indicate valid fields 
        success: function(label) {
            // set  as text for IE 
            //label.html(" ").addClass("checked");
            label.remove();
        }
    });

    if ($('input:focus').length === 0) {
        $('#QuickLoginEmail input').focus();
    }




    var forgotvalidator = $("#ForgotPasswordForm").validate({
        rules: {
            email: {
                required: true,
                email: true
            },
        },
        errorPlacement: function(error, element) {
            error.appendTo(element.parent().next());
        },
        submitHandler: function() {
            $.ajax({
                url: '/reset/json/req',
                type: 'POST',
                data: {
                    Email: $('#ForgotEmail').val(),
                },
                success: function(data) {
                    if (data.result === true) {
                        $('#ForgotPasswordForm').text('Success! Check your email for password reset instructions');
                    }
                    else {
                        $('#tryagain').show();
                        $('#break').show();
                    }
                }
            });
        },
        success: function(label) {
            label.remove();
        }
    });

    $('#QuickLoginForm').submit(function(event) {
        var url = document.URL;
        event.preventDefault();
        var emailbox = $(this).parent( ).find('input[name=LoginEmail]');
        var passbox = $(this).parent( ).find('input[name=LoginPassword]');
        var shaObj = new jsSHA(passbox.val( ), "ASCII");
        var hash = shaObj.getHash("SHA-256", "HEX");
        $.ajax({
            url: '/login',
            type: 'POST',
            data: {
                LoginEmail: emailbox.val(),
                LoginPassword: hash,
                RememberMe: ($('.remember>input[type=checkbox]').is(':checked'))? 1 : 0
            },
            success: function(data, textStatus, jqXHR) {
                if (data === 'Invalid Login') {
                    $('#QuickLoginFormLoginFailed').show();
                    $('#break').show();
                }
                else
                    top.location = url;
            },
            error: function(data) {
                $('#QuickLoginFormLoginFailed').show();
                $('#break').show;

            }
        });
        return false;
    });


});
