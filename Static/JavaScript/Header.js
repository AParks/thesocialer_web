var Header = function( ) {
};

Header.showFailedLogin = function( ) {
    $('#QuickLoginFormLoginFailed').show( );
    $('#QuickLoginForm').show( );
}
$(function() {


    if (Viewer.userId != -1) {
        mixpanel.identify("" + Viewer.userId + "");
        Viewer["$email"] = Viewer.email;
        mixpanel.people.set(Viewer);
    }
 /*   UserVoice = window.UserVoice || [];
    UserVoice.push(['showTab', 'classic_widget', {
            mode: 'full',
            primary_color: '#88b968',
            link_color: '#6887b9',
            default_mode: 'support',
            forum_id: 212307,
            tab_label: 'Feedback & Support',
            tab_color: '#88b968',
            tab_position: 'bottom-left',
            tab_inverted: false
        }]);*/
 
    
 /* 
 var url = document.URL;
    var start = url.indexOf('.com');
    var referring = url.substring(start + 4, url.length);
    mixpanel.track_links('.NavigationLink.first', 'popup tab click - live site', {"previous page": referring, "user_id": Viewer.userId});
    mixpanel.track_links('.NavigationLink.second', 'host a popup click - live site', {"previous page": referring, "user_id": Viewer.userId});
    mixpanel.track_links('.NavigationLink.third', 'around the city click - live site', {"previous page": referring, "user_id": Viewer.userId});
    mixpanel.track_links('.NavigationLink.last', 'community tab click - live site', {"previous page": referring, "user_id": Viewer.userId});


*/

    //facebook logout
    $('#fb_logout').click(function() {
        //.reload(true);
        FB.getLoginStatus(function(response) {
            if (response.status === 'connected') {
                // the user is logged in and has authenticated your
                // app, and response.authResponse supplies
                // the user's ID, a valid access token, a signed
                // request, and the time the access token 
                // and signed request each expire
                var uid = response.authResponse.userID;
                var accessToken = response.authResponse.accessToken;
                if (response.session) {
                    FB.logout(function(response) {
                        window.location = "/logout";
                    });
                }
                else
                    window.location = "/logout";

            } else if (response.status === 'not_authorized') {
                // the user is logged in to Facebook, 
                // but has not authenticated your app
                FB.logout(function(response) {
                    window.location = "/logout";
                });
            } else {
                // the user isn't logged in to Facebook
                window.location = "/logout";

            }
        });


    });
    function calculateMargins() {
        var screen_width = viewportSize.getWidth();
        var outer_width = 0.75 * screen_width;
        var inner_width = $('#MemberLoginContainer').width();

        var marginLeft = parseInt((outer_width - inner_width) / 2);
        $('#MemberLoginContainer').css('margin-left', marginLeft);

    }

    function resend() {
        $('#confirm').click(function() {
            var failedForm = $(this).parent();
            var email = $(this).attr('email');
            var name = $(this).attr('firstname');
            var user_id = $(this).attr('user_id');

            $.ajax({
                url: '/register',
                type: 'POST',
                data: {
                    action: 'resend_email',
                    firstName: name,
                    emailAddress: email,
                    user_id: user_id
                },
                success: function(data) {
                    if (data) {
                        failedForm.html(data);
                        failedForm.show();
                    } else
                        top.location = '/confirm';
                }
            });
        });
    }


    function login() {
        FB.api('/me', function(response) {
            var url = document.URL;

            $.ajax({
                url: '/login/fb',
                type: 'POST',
                data: {
                    user_info: response,
                    fb_id: response.id
                },
                success: function(data, textStatus, jqXHR) {
                    top.location = url;
                    mixpanel.identify("" + Viewer.userId + "");
                    Viewer["$email"] = Viewer.email;
                    mixpanel.people.set(Viewer);


                }
            });
        });

    }

    //friend reqests

    $('#friend_request').click(function() {
        FB.ui({
            method: 'apprequests',
            message: 'Join thesocialer.com! ',
            title: 'Invite your friends to The Socialer',
        },
                function(response) {
                    if (response.request && response.to) {
                        var request_ids = [];
                        for (i = 0; i < response.to.length; i++) {
                            var temp = response.request + '_' + response.to[i];
                            request_ids.push(temp);
                        }
                        var requests = request_ids.join(',');
                        //   $.post('handle_requests.php', {uid: '<?php echo $user; ?>', request_ids: requests}, function(resp) {
                        // callback after storing the requests
                        //   });
                    } else {
                        alert('canceled');
                    }
                });
        return false;
    });

    //facebook login
    $('div.fb-login').click(function() {
        FB.getLoginStatus(function(response) {
            if (response.status === 'connected') {
                // the user is logged in and has authenticated your
                // app, and response.authResponse supplies
                // the user's ID, a valid access token, a signed
                // request, and the time the access token 
                // and signed request each expire
                var uid = response.authResponse.userID;
                var accessToken = response.authResponse.accessToken;
                login();
            } else if (response.status === 'not_authorized') {
                // the user is logged in to Facebook, 
                // but has not authenticated your app
                FB.login(function(response) {
                    // handle the response
                    if (response.authResponse)
                        login();

                }, {scope: 'email,user_birthday, user_education_history, user_location, user_about_me'});

            } else {
                // the user isn't logged in to Facebook.
                FB.login(function(response) {
                    // handle the response
                    if (response.authResponse)
                        login();


                }, {scope: 'email,user_birthday, user_education_history, user_location,user_about_me'});

            }
        });

    });


    $('#HelpButton').bind('click', function() {
        $('.HowItWorks').toggle();
    });

    $('.HowItWorks').find('.CloseWindow').bind('click', function() {
        $(this).parent().hide();
    });






    $('.ForgotLink').click(
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
                    action: 'register',
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
                    if (data) {
                        $('#RegFailed').html(data);
                        $('#RegFailed').show();
                        $('.inner').find('#break').show();
                        resend();


                    }
                    else
                        top.location = '/confirm';
                },
                error: function(data) {
                    console.log(data);


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
            url: '/login/normal',
            type: 'POST',
            data: {
                LoginEmail: emailbox.val(),
                LoginPassword: hash,
                RememberMe: ($('.remember>input[type=checkbox]').is(':checked')) ? 1 : 0
            },
            success: function(data, textStatus, jqXHR) {
                if (data) {
                    $('#QuickLoginFormLoginFailed').html(data);
                    
    $('.ForgotLink').click(
            function() {
                $('#QuickLoginFormLoginFailed').hide();
                $('#break').hide();
                $('#QuickLoginForm').slideUp();
                $('#ForgotPasswordForm').show("slow");
            }
    );
                    $('#QuickLoginFormLoginFailed').show();
                    $('#break').show();
                    resend();
                }
                else {
                    top.location = url;
                    mixpanel.identify("" + Viewer.userId + "");
                    Viewer["$email"] = Viewer.email;
                    mixpanel.people.set(Viewer);
                }

            },
            error: function(data) {
                $('#QuickLoginFormLoginFailed').show();
                $('#break').show;

            }
        });
        return false;
    });


});
