var LoginForm = function( ) {
    var inputs = $('input', '#registerform');
    var joinbutton = $('button[name=Join]');
    var eventList = $('.EventList');
    var eventSkeleton = eventList.find('li.LocationSkeleton').removeClass('LocationSkeleton').remove();
    var regform = $('#registrationform');
    var passcheck = $('#passcheck');



    function init( ) {
        Main.overlay();
        //var loginbox = $('#MemberLoginContainer');
        //loginbox.css('position', 'absolute');
        //loginbox.css('left', '50%');
        $('input[type=text][id=college]').css('width', '55%');


        //show the password if checkbox is checked
        passcheck.click(function() {
            var password = $('#password');
            var type = 'password';
            if ($(this).is(':checked'))
                type = 'text';

            password.clone().attr('type', type).insertAfter(password)
                    .prev().remove();
        });

        loadEventsForDate(Main.formatDate(new Date()));

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
                    required: "Enter your last name",
                },
                password: {
                    required: "Provide a password",
                    rangelength: jQuery.format("Enter at least {0} characters")
                },
                email: {
                    email: "Enter a valid email",
                    required: "Enter a valid email",
                    minlength: "Enter a valid email",
                    remote: jQuery.format("{0} is already in use")
                },
                month: {
                    required: "Enter your birthdate"
                },
                day: {
                    required: "Enter your birthdate"
                },
                year: {
                    required: "Enter your birthdate"
                },
                gender: {
                    required: "Please select a gender"
                }
            },
            errorPlacement: function(error, element) {
                if (element.attr("name") == "month" || element.attr("name") == "day" || element.attr("name") == "year") {
                    if($('label:contains(birthdate)').length == 0)
                        element.parent().parent().append(error);
                    
                }
                else if ( element.is(":radio") ) {
                    console.log(element.parent().parent())
                                            element.parent().parent().append(error);

//                    error.insertAfter( element.parent().parent() ); 
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
                        top.location = '/trending';
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
    }


    function loadEventsForDate(selectedDate) {
        Main.fetchFromServer('/locations/json/forDate', {date: selectedDate, limit: 9}, receiveEventsForDate);
    }

    function receiveEventsForDate(response) {
        for (var i = 0, len = response.locations.length; i < len; i++) {
            newEvent(response.locations[i]);
        }
        $('#TrendingEvents').animate({
            opacity: 1
        }, 2000, function() {
        });
        ;
    }

    function newEvent(locationInfo) {
        var newLocation = eventSkeleton.clone(true);

        var locationImage = newLocation.find('.LocationImage');
        locationImage.prepend('<img src="/Photos/Locations/' + locationInfo.image + '" />');

        locationImage.bind('click', function() {
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
                    });
                    $(this).find('.CommentContainer').animate({
                        opacity: 0.9,
                        bottom: '-6px'
                    }, 150, function() {
                    });
                },
                function() {
                    $(this).find('.TitleLocationContainer').animate({
                        opacity: 1,
                        top: '0'
                    }, 150, function() {
                    });
                    $(this).find('.CommentContainer').animate({
                        opacity: 1,
                        bottom: '0'
                    }, 150, function() {
                    });
                });

        if (locationInfo.userStatus) {
            if (locationInfo.userStatus == 'yes') {
                newLocation.find('.AttendingCount').addClass('active');
            }
        }

        var attendingCounts = newLocation.find('ul.AttendingCounts');
        attendingCounts.find('li.AttendingCount').html('<strong>' + locationInfo.attendanceCounts.yes + '</strong> Going');
        newLocation.removeClass('hide');
        newLocation.appendTo(eventList);
    }

    function newAttendee(container, userId) {
        var newImage;
        newImage = $('<li data-user-id="' + userId + '"><img src="/photo/' + userId + '/Small" /></li>');
        container.append(newImage);
    }


    var collegebox = $('#selectcollege');
    function parseCollegeData(data) {
        var dataarray = data.split("\n");
        for (college in dataarray) {
            addCollegeOption(dataarray[college]);
        }
        collegebox.select2({
            placeholder: "Where did you go?",
            minimumInputLength: 3,
            allowClear: true
        });
        collegebox.parent().find('.select2-container').css('width', '250px');
    }
    function addCollegeOption(college) {
        collegebox.append('<option value="' + college + '">' + college + '</option>');
    }
    function showForgotPassword() {
        $('#QuickLoginForm').slideUp();
        $('#ForgotPasswordBox').show("slow");
    }


    init();
};

$(function( ) {
    new LoginForm( );
});
