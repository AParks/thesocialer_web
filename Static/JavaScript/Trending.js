var Home = function() {
    var attendanceManager = new AttendanceManager( );
    var activeDate = formatDate(new Date());
    var eventDays = $('li', '#EventDays').filter(':not(.last)');
    var eventList = $('.EventList');
    var eventSkeleton = eventList.find('.LocationSkeleton').removeClass('LocationSkeleton').remove();
    var events = eventList.find('.Event');
    var CommentSkeleton = $('.CommentSkeleton').removeClass('CommentSkeleton').remove();
    var opacitySupport = $.support.opacity;

    function init() {
        eventDays.click(function( ) {
            $('.WelcomeBack .dropdown-toggle').find('span').text($(this).text());
            selectDate($(this).attr('data-date'), true);

        });
        selectDate(activeDate, true);
        initFeaturedEvents();
        calculateMargins();
        $(window).resize(function() {
            calculateMargins();

        });
    }

    function calculateMargins() {
      if($('#EventDays').parent().width() <= 591){
            $('#EventDays').width(130); 
        }
        else{
            $('#EventDays').css('width', '100%'); 
        }
        var screen_width = viewportSize.getWidth();
        $('body').width(screen_width);

        var outer_width = 0.75 * screen_width;
        var singleTile = 286;

        var numTiles = Math.floor(outer_width / singleTile);
        if (!numTiles) {
            numTiles = 1;
            outer_width = screen_width;
            $('#MainBody').width(screen_width);

           // $('.Home').width(286);

        }
        var inner_width = numTiles * singleTile;

        var marginLeft = parseInt((outer_width - inner_width) / 2);
            $('.Home').css('margin-left', marginLeft);
        marginLeft_event_days = parseInt((outer_width - $('#EventDays').width()) / 2);
        $('.WelcomeBack').css('margin-left', marginLeft_event_days);

    }


    function loadTopCommentForLocationAndDate(locId) {
        Main.fetchFromServer('/network/json/getcomments', {location: locId, date: activeDate, limit: 1}, receiveComment);
    }

    function receiveComment(response) {
        if (response.comments.length > 0) {
            var comment = newComment(response.comments[0]);
            comment.removeClass('hide');
            $('.Event[data-location-id=' + response.comments[0].locationid + ']').append(comment);
            comment.animate({
                bottom: '0'
            }, 500, function() {
            });
        }
    }
    function newComment(comment) {
        var newComment = CommentSkeleton.clone(true);
        newComment.attr('data-comment-id', comment.id);
        //newComment.find('span.UserName').text(comment.user.firstName);
        //newComment.find('a.UserLink').attr('href', '/profile/' + comment.user.userId);
        //newComment.find('img').attr('src','/photo/' + comment.user.userId + '/Small');


        //find the length of the comment without the html
        var link_start = comment.message.indexOf('<');
        var link_start_inside = comment.message.indexOf('>');
        var link_end_inside = comment.message.indexOf('</');
        var link_end = comment.message.indexOf('>', link_start_inside + 1);

        var before_link_tag = comment.message.substring(0, link_start);
        var inside_link_tag = comment.message.substring(link_start_inside + 1, link_end_inside);
        var after_link_tag = comment.message.substring(link_end, comment.message.length);
        var actual_msg_length = (before_link_tag + inside_link_tag + after_link_tag).length;
        var before_and_inside = (before_link_tag + inside_link_tag).length;

        var max_length = 49;
        if (actual_msg_length <= max_length) {
            newComment.find('.Comment').append(comment.message);
        }
        else {
            if (before_and_inside <= max_length) { //entire message is > 54
                var remaining = max_length - before_and_inside;
                newComment.find('.Comment').append(comment.message.substring(0, link_end + remaining));
            }
            else if (before_link_tag.length <= max_length) { //before and link is > 54
                newComment.find('.Comment').append(comment.message.substring(0, link_start));
            } else { //before > 54
                newComment.find('.Comment').append(comment.message.substring(0, 54));
            }
            newComment.find('.Comment').append("<a id='more' style='text-decoration: underline; color: black'>...more</a>");

        }

        newComment.find('.Comment').linkify();

        return newComment;
    }

    function selectDate(selectedDate, showLoading) {
        activeDate = selectedDate;
        eventDays.removeClass('selected');
        eventDays.filter('[data-date=' + selectedDate + ']').addClass('selected');
        events.remove( );
        events = [];
        if (showLoading) {
            $('<li class="Loading"><img src="/Static/Images/General/loading.gif" /><br />Loading...</li>').appendTo(eventList);
        }
        loadEventsForDate(selectedDate);

        window.setTimeout(function() {
            $('.Comment a[id=more]').attr('href', function()
            {
                return $(this).parent().parent().prev().attr('href');
            }
            );
        }, 5000);

    }

    function loadEventsForDate(selectedDate) {
        Main.fetchFromServer('/locations/json/forDate', {date: selectedDate, limit: 18}, receiveEventsForDate);
    }

    function receiveEventsForDate(response) {
        for (var i = 0, len = response.locations.length; i < len; i++) {
            newEvent(response.locations[i]);
        }

        eventList.find('li.Loading').remove();
        events = eventList.find('.Event');
        eventList.css('opacity', '0');
        events.removeClass('hide');
        eventList.animate({
            opacity: 1
        }, 1500, function() {
        });

        for (var i = 0, len = response.locations.length; i < len; i++) {
            loadTopCommentForLocationAndDate(response.locations[i].id);
        }
    }

    function newEvent(locationInfo) {
        var newLocation = eventSkeleton.clone(true);

        var locationImage = newLocation.find('.LocationImage');
        locationImage.prepend('<img src="/Photos/Locations/' + locationInfo.image + '" />');
        locationImage.parent().attr('href', '/location/' + locationInfo.id + '/' + activeDate);

        locationInfo['eventDate'] = activeDate;
//        mixpanel.track_links('#LocationImageLink', 'trending event click - live site', locationInfo);
        locationImage.bind('click', function() {
            toggleAttendance($(this).prev().find('.AttendingCount'));
            //         mixpanel.track('trending event test');
        });

        var text = newLocation.find('.Comment').text();
        newLocation.find('.Event').attr('data-location-id', locationInfo.id);
        newLocation.find('.EventTitle').text(locationInfo.name);
        newLocation.find('.EventLocation').text(locationInfo.streetAddress); //streetAddress
        ;


        newLocation.hover(
                function() {
                    var locTopBar = $(this).find('.TitleLocationContainer');
                    if (opacitySupport) {
                        locTopBar.animate({
                            opacity: 0.9,
                            top: '-4px'
                        }, 150);
                        $(this).find('.CommentContainer').animate({
                            opacity: 0.9,
                            bottom: '-6px'
                        }, 150);
                    }
                    else {
                        locTopBar.animate({top: '-4px'}, 150);
                        $(this).find('.CommentContainer').animate({bottom: '-6px'}, 150);
                    }

                    /*   if (($(this).find('.CommentContainer').length == 0)) {
                     var trend = $(this).find('.TrendContainer');
                     if ((trend.length == 0)) {
                     $(this).find('.Event').append('<img src="/Static/Images/trend.png" class="TrendContainer" />');
                     trend = $(this).find('.TrendContainer');
                     }
                     trend.animate({bottom: '0'}, 300);
                     } */
                },
                function() {
                    if (opacitySupport) {
                        $(this).find('.TitleLocationContainer').animate({
                            opacity: 1,
                            top: '0'
                        }, 150);
                        $(this).find('.CommentContainer').animate({
                            opacity: 1,
                            bottom: '0'
                        }, 150);
                    }
                    else {
                        $(this).find('.TitleLocationContainer').animate({
                            top: '0'
                        }, 150);
                        $(this).find('.CommentContainer').animate({
                            bottom: '0'
                        }, 150);
                    }
                    $(this).find('.TrendContainer').animate({
                        bottom: '-100px'
                    }, 300);
                });

        if (locationInfo.userStatus) {
            if (locationInfo.userStatus == 'yes') {
                newLocation.find('.AttendingCount').addClass('active');
            }
        }

        var buttons = newLocation.find('.AttendingCount');
        buttons.click(function() {
            toggleAttendance($(this));
        });

        var attendingCounts = newLocation.find('ul.AttendingCounts');
        attendingCounts.find('li.AttendingCount').html('<strong>' + locationInfo.attendanceCounts.yes + '</strong>');
        newLocation.appendTo(eventList);
    }

    function toggleAttendance(button) {

        var locationId = button.closest('.Event').attr('data-location-id');
        if (Viewer.userId == -1)
            $('#myModal').modal('show');
        else if (button.hasClass('active')) {
            var attendingCountContainer = button.find('strong');
            attendingCountContainer.text(parseInt(attendingCountContainer.text(), 10) - 1);
            attendanceManager.setAttendanceStatus(locationId, activeDate, 'no');
            button.removeClass('active');
        }
        else {
            var attendingCountContainer = button.find('strong');
            attendingCountContainer.text(parseInt(attendingCountContainer.text(), 10) + 1);
            attendanceManager.setAttendanceStatus(locationId, activeDate, 'yes');
            button.addClass('active');
        }
    }
    function hoverOverMember( ) {
        var container = $(this);
        var memberName = container.find('div.MemberName');
        if (memberName.length) {
            memberName.show( );
        }
        else {
            var nameDiv = $('<div class="MemberName"></div>');
            nameDiv.hide().addClass('mouseOver');
            container.append(nameDiv);

            Main.fetchFromServer(
                    '/user/' + $(this).attr('data-user-id'),
                    {fields: 'firstName,lastName'},
            function(response) {
                nameDiv.text(response.firstName + ' ' + response.lastName);
                nameDiv.css('left', -1 * (nameDiv.outerWidth( ) - container.outerWidth( )) / 2);
                if (nameDiv.hasClass('mouseOver')) {
                    nameDiv.show();
                }
            });
        }
    }

    function hoverOutMember( ) {
        $(this).find('div.MemberName').hide( ).removeClass('mouseOver');
    }

    function formatDate(dateObject) {
        return dateObject.getFullYear() + '-' + (dateObject.getMonth() + 1) + '-' + dateObject.getDate();
    }

    function featuredResponse(response) {

        $('.MaybeCount strong').text(response.maybe);
    }

    function initFeaturedEvents( ) {
        $('#FeaturedEvent').find('.AttendingCounts li').bind('click', function() {
            if ($(this).attr('active') == '1') {
                attendanceManager.setFeaturedEventAttendanceStatus(
                        $('#FeaturedEvent').attr('data-event-id'),
                        'no',
                        function(response) {
                        }
                )
                var num = $(this).find('strong');
                num.text(parseInt(num.text()) - 1);
                $(this).attr('active', '0')
            }
            else {
                attendanceManager.setFeaturedEventAttendanceStatus(
                        $('#FeaturedEvent').attr('data-event-id'),
                        $(this).attr('data-status'),
                        function(response) {
                        }
                )
                var num = $(this).find('strong');
                num.text(parseInt(num.text()) + 1);
                $(this).attr('active', '1')

                var other = $('#FeaturedEvent .AttendingCounts li').not(this);
                if (other.attr('active') == '1') {
                    var onum = other.find('strong');
                    onum.text(parseInt(onum.text()) - 1);

                    other.attr('active', '0')
                }
            }
        });
        $('#FeaturedEvent_Markup').bind('click', function() {
            top.location = '/location/featured/' + $('#FeaturedEvent').attr('data-event-id');
        });
        $('#FeaturedEvent_Markup').find('a').attr('target', '_blank');

        $('#FeaturedEvent_Markup').hover(
                function() {
                    $(this).find('.FeaturedEventBottomBar').animate({height: '48px'}, 150);
                },
                function() {
                    $(this).find('.FeaturedEventBottomBar').animate({height: '44px'}, 150);
                }
        );
    }

    init();
};

$(function() {
    new Home( );
});
