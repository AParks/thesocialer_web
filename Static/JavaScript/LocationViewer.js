LocationViewer = function(loc, date, userStatus) {
    var geocoder = new google.maps.Geocoder( );
    var _this = this;
    var attendanceManager = new AttendanceManager( );
    var LocationCommentList = $('.LocationCommentList');

    var LocationCommentSkeleton = LocationCommentList.find('div.LocationCommentSkeleton').removeClass('LocationCommentSkeleton').remove();
    var comments;
    var existsTopComment = false;
    var supportsHistAPI = (!!(window.history && window.history.replaceState));

    function toggleAttendance(toggled) {
        if (Viewer.userId == -1)
            $('#myModal').modal('show');
        else {
            var favcount = $('.LocationAttendingCount');
            if (toggled.attr('data-status') === 'yes') {
                toggled.removeClass('active');
                toggled.attr('data-status', 'no');
                attendanceManager.setAttendanceStatus(loc.id, date, 'no');
                $('.AttendeesYes').find('a[href="/profile/' + Viewer.userId + '"]').remove();
                var count = parseInt(favcount.text()) - 1;
                favcount.text(count);
            }
            else {
                toggled.addClass('active');
                toggled.attr('data-status', 'yes');
                attendanceManager.setAttendanceStatus(loc.id, date, 'yes');
                var userlink = '<a href="/profile/' + Viewer.userId + '"><img src="/photo/' + Viewer.userId + '/Small" class="UserPhoto" /> </a>';
                $('.AttendeesYes').append(userlink);
                var count = parseInt(favcount.text()) + 1;
                favcount.text(count);

            }
        }


        //   attendanceManager.setAttendanceStatus(loc.id, date, 'yes');
        //   favcount.text(parseInt(favcount.text()) + 1);            


    }



function setUpEventButtons() {
    $('.FavoriteEvent').bind('click', function() {
        toggleAttendance($(this));
    });

    $('.ShareEvent').bind('click', function() {
        if (Viewer.userId != -1)
            eventSharer.showFriendSelector();
        else {
            $('#myModal').modal('show');
        }
    });
    $('.ShareEvent').tooltip();
    $('.FavoriteEvent').tooltip();
    $('#tags').popover({
        trigger: 'hover',
        html: true,
        content: function() {
            return $('.popover').html();
        }
    });
}

function init( ) {


    // var buttons = $('AttendingButtons_Yes');
    //buttons.hover(function() {
    //    $(this).addClass('hover');
    //}, function() {
    //    $(this).removeClass('hover');
    //})


    $('.NewComment').autosize();




    if (loc.userLikes) {
        $('.LocationLikeButton').addClass('active');
    }
    $('.LocationLikeButton').bind('click', function() {
        if ($(this).hasClass('active')) {
            $.ajax({
                url: '/network/json/unlikelocation',
                type: 'GET',
                data: {locationId: loc.id},
                success: function(data) {
                },
                error: function(data) {
                    console.log(data.e);
                }
            });
            $('.LocationLikeCount').html(--loc.likes);
            $(this).removeClass('active').text("like");
            $('.LocationLikeText').text("");
        }
        else {
            $.ajax({
                url: '/network/json/likelocation',
                type: 'GET',
                data: {locationId: loc.id},
                success: function(data) {
                },
                error: function(data) {
                    console.log(data.e);
                }
            });
            $('.LocationLikeCount').html(++loc.likes);
            $(this).addClass('active').text("unlike");
            $('.LocationLikeText').text("You like this.");
        }
    });




    //suggest an event
    $('textarea').keydown(function(event) {
        if (event.which == 13) { // enter key pressed
            event.preventDefault();
            var comment = $(this).val();
            if (comment == "")
                return;
            var commentbox = $(this);
            var locid = loc.id;

            _this.makeComment(locid, date, comment);
            $(this).val('');  //clear the text from the text area
            $(this).blur();   //remove focus from the text area

        }
    });

    if (Viewer.userId != -1) {
        $('.Attendees').find('a').hover(hoverOverMember, hoverOutMember);
    }
    _this.drawMap( );
    _this.getComments();
    //    loadTopCommentForLocationAndDate();

}

this.getComments = function() {
    Main.fetchFromServer('/network/json/getcomments', {date: date, location: loc.id}, _this.receiveComments);
}

// TODO Consider not calling getComments every time a new comment or reply is added. instead, add it artificially onto the DOM

this.makeComment = function(locId, date, comment) {
    Main.fetchFromServer('/network/json/makecomment', {location: loc.id, date: date, message: comment}, _this.getComments);
       if ($('.FavoriteEvent').attr('data-status') != 'yes')
           toggleAttendance();
}

this.makeReply = function(commentId, replytext) {
    Main.fetchFromServer('/network/json/makereply', {commentId: commentId, message: replytext}, _this.getComments);
}

this.receiveComments = function(response) {
    LocationCommentList.empty();
    var lastComment;

    for (var i = 0, len = response.comments.length; i < len; i++) {
        //alert(JSON.stringify(response.comments[i], null, 4));
        lastComment = _this.newComment(response.comments[i]);
    }

    if (response.comments.length > 0)
        lastComment.addClass('last');



    if (!existsTopComment && response.comments.length > 0) {
        $('.LocationCommentContainer[data-user-id=' + Viewer.userId + ']').find('.heart:not(.active)').click();
        //  loadTopCommentForLocationAndDate();
        existsTopComment = true;
    }
}

this.newComment = function(comment) {
    var newComment = LocationCommentSkeleton.clone(true);
    newComment.attr('data-comment-id', comment.id);
    newComment.attr('data-user-id', comment.user.userId);
    newComment.find('a.UserName').text(comment.user.firstName + " " + comment.user.lastName);

    newComment.find('a.UserName').attr('href', '/profile/' + comment.user.userId);
    newComment.find('img').attr('src', '/photo/' + comment.user.userId + '/Medium');
    newComment.find('.Comment').html(comment.message);
    newComment.find('.Comment').linkify();
    newComment.find('.Comment').find('a').attr('target', '_blank');
    newComment.find('.LikeCount').text(comment.likes.length);
    if ($.inArray(Viewer.userId, comment.likes) != -1) {
        newComment.find('.heart').toggleClass('active');
    }

    if (Viewer.userId == comment.user.userId) {
        newComment.find('.eventcontainer').prepend('<div class="DeleteComment">✖</div>');
        newComment.find('.DeleteComment').bind('click', function() {
            $.ajax({
                url: '/network/json/deletecomment',
                type: 'GET',
                data: {commentId: comment.id},
                success: function(data) {
                    newComment.remove();
                },
                error: function(data) {
                    console.log(data.e);
                }
            });
        });
    }
    if (Viewer.userId != -1) {
        newComment.find('.heart').bind('click', function() {
            if ($(this).hasClass('active')) {
                $.ajax({
                    url: '/network/json/unlikecomment',
                    type: 'GET',
                    data: {commentId: comment.id},
                    success: function(data) {
                    },
                    error: function(data) {
                        console.log(data.e);
                    }
                });
                newComment.find('.LikeCount').text(--comment.likes.length);
                $(this).removeClass('active');
            }
            else {
                $.ajax({
                    url: '/network/json/likecomment',
                    type: 'GET',
                    data: {commentId: comment.id},
                    success: function(data) {
                    },
                    error: function(data) {
                        console.log(data.e);
                    }
                });
                newComment.find('.LikeCount').text(++comment.likes.length);
                $(this).toggleClass('active');
            }
        });
    }
    else {
        newComment.find('.heart').css('cursor', 'default');
    }


    var d = new Date(0);
    d.setUTCSeconds(comment.posted_at);
    var months = new Array();
    months[0] = "Jan";
    months[1] = "Feb";
    months[2] = "Mar";
    months[3] = "Apr";
    months[4] = "May";
    months[5] = "Jun";
    months[6] = "Jul";
    months[7] = "Aug";
    months[8] = "Sep";
    months[9] = "Oct";
    months[10] = "Nov";
    months[11] = "Dec";
    var month = months[d.getMonth()];
    var day = d.getDate();
    var hour = d.getHours();
    var ampm = hour / 12.0 >= 1 ? 'PM' : 'AM';
    var min = d.getMinutes();
    min = min < 10 ? '0' + min : min;
    newComment.find('.Timestamp').text(month + ' ' + day + ' ' + hour % 12 + ':' + min + ampm);

    if (comment.user.College) {
        newComment.find('span.UserCollege').text(comment.user.College);
    }
    var replyList = newComment.find('.Replies');

    for (reply in comment.replies) {
        var markup = '<div class="reply">'
                + comment.replies[reply].first_name + ': ' + comment.replies[reply].message + ' '
                + ((comment.replies[reply].user_id == Viewer.userId) ? '<span reply-id="' + comment.replies[reply].reply_id + '" class="DeleteReply">✖</span>' : '')
                + '</div>';
        replyList.append(markup);

    }

    newComment.find('.DeleteReply').bind('click', function() {
        var replybox = $(this).parent();
        $.ajax({
            url: '/network/json/deletereply',
            type: 'GET',
            data: {replyId: $(this).attr('reply-id')},
            success: function(data) {
                replybox.remove();
            },
            error: function(data) {
                console.log(data.e);
            }
        });
        
    });
    newComment.appendTo(LocationCommentList);
    setUpEventButtons();
    return newComment;
}

this.drawMap = function( ) {
    geocoder.geocode({'address': loc.streetAddress + ' ' + loc.cityName + ' ' + loc.stateName}, function(results, status) {
        if (status == google.maps.GeocoderStatus.OK) {
            map.setCenter(results[0].geometry.location);
            var marker = new google.maps.Marker({
                map: map,
                position: results[0].geometry.location
            });

            var link = '<h2>' + loc.name + '<h2>';
            var infowindow = new google.maps.InfoWindow({
                content: link,
                maxWidth: 100
            });
            google.maps.event.addListener(marker, 'click', function() {
                infowindow.open(map, this);
            });
        }
    });

    var options = {
        zoom: 18,
        mapTypeId: google.maps.MapTypeId.ROADMAP,
        streetViewControl: false,
        mapTypeControl: false
    };
    var map = new google.maps.Map(document.getElementById("LocationMap"), options);
};

function loadTopCommentForLocationAndDate( ) {
    Main.fetchFromServer('/network/json/gettopcomments', {location: loc.id, date: date, limit: 1}, receiveTopComment);
}
function receiveTopComment(response) {

    if (response.comments.length > 0) {
        //  $('.add-event').addClass('hide');
        var comment = newTopComment(response.comments[0]);
        comment.slideDown(1000, function() {
            comment.removeClass('hide');
        });

        existsTopComment = true;

    }
    else {
        existsTopComment = false;
    }
}
function newTopComment(comment) {

    var newComment = $('.TopCommentContainer');
    newComment.attr('data-comment-id', comment.id);
    newComment.find('a.UserName').text(comment.user.firstName + " " + comment.user.lastName);
    if (comment.user.College) {
        newComment.find('span.UserCollege').text(comment.user.College);
    }
    var ulink = newComment.find('a.UserName');
    ulink.attr('href', '/profile/' + comment.user.userId);
    ulink.attr('data-user-id', comment.user.userId);

    newComment.find('img').attr('src', '/photo/' + comment.user.userId + '/Small');
    var commentdiv = newComment.find('.Comment');
    commentdiv.html(comment.message);
    commentdiv.linkify();
    commentdiv.find('a').attr('target', '_blank');
    return newComment;
}

function hoverOverMember(event) {
    var container = $(this);
    var memberName = container.find('div.MemberName');
    if (memberName.length) {
        memberName.show( );
    }
    else {
        var nameDiv = $('<div class="MemberName"></div>');
        nameDiv.hide().addClass('mouseOver');
        container.append(nameDiv);

        Main.fetchFromServer('/user/' + $(this).attr('data-user-id'), {fields: 'firstName,lastName,College'},
        function(response) {
            nameDiv.html('<span class="UserName">' + response.firstName + '</span><br />' + '<span class="UserCollege">' + response.College + '</span>');
            $(document).mousemove(function(e) {
                nameDiv.css({'top': e.pageY + 20, 'left': e.pageX - 10});
            });
            nameDiv.css({'top': event.pageY + 20, 'left': event.pageX - 10});
            //nameDiv.css('left', -1 * ( nameDiv.outerWidth( ) - container.outerWidth( ) )/2 );
            if (nameDiv.hasClass('mouseOver')) {
                nameDiv.show();
            }
        });
    }
}

function hoverOutMember( ) {
    $(this).find('div.MemberName').hide( ).removeClass('mouseOver');
}


init( );
}

$(function( ) {
    console.log("user status is " + userStatus);
    if (userStatus === 'yes') {
        $('.FavoriteEvent').attr('data-status', userStatus);
        $('.FavoriteEvent').addClass('active');
    }
    var lv = new LocationViewer(loc, date, userStatus);
});
