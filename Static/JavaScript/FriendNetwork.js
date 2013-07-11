var FriendNetwork = function() {
    var activeDate = formatDate(new Date());

    var userRecommendationList = $('ul.UserRecommendationList');
    var userRecommendationSkeleton = userRecommendationList.find('li.UserRecommendationSkeleton').removeClass('UserRecommendationSkeleton').remove();
    var friend_start = 0;

    function init() {

//        getFbFriendList(friend_start);

        $(window).scroll(function() {
            if ($(window).scrollTop() + $(window).height() == $(document).height()) {
                getFbFriendList(friend_start);
            }
        });
         loadUserRecommendations();
    }



    function getFbFriendList(start) {
        console.log(start);
        var profilePicsDiv = $('#profile_pics');

        FB.getLoginStatus(function(response) {
            if (response.status != 'connected') {
                profilePicsDiv.append('<em>You are not connected</em>');
                return;
            }

            FB.api({method: 'friends.get'}, function(result) {
                console.log('friends.get response' + result);
                var markup = '';
                var numFriends = result ? Math.min(10, result.length) : 0;
                if (numFriends > 0) {
                    for (var i = start; i < start + numFriends; i++) {

                        markup += ('<div><img src="https://graph.facebook.com/' +
                                result[i] + '/picture?type=square" /><button class="invite" u-id="'+ result[i] + '">Invite</button></div><br>');
                    }
                    friend_start += numFriends;

                }
                profilePicsDiv.append(markup);
                $('.invite').bind('click', function() {
                    console.log('hello');
                    FB.ui({
                        method: 'send',
                        link: 'https://thesocialer.com',
                        to: $(this).attr('u-id')
                    });
                });
            });
        });


    }

    function loadUserRecommendations() {
        Main.fetchFromServer('/recommendations/user/json/get', {limit: 10}, receiveUserSuggestions);
    }

    function reloadRecommendations() {
        // 0 to 702px
        userRecommendationList.animate({
            height: '0'
        }, 500, function() {
            userRecommendationList.empty();
            loadUserRecommendations();
        });


    }
    $('.SeeMoreButton').bind('click', reloadRecommendations);

    function receiveUserSuggestions(response) {
        if (response.users.length == 0) {
            userRecommendationList.append("<p>No recommendations for you.</p>");
            userRecommendationList.animate({
                height: '50px'
            }, 200, function() {
            });
            $(".SeeMoreButton").remove();
            return;
        }
        userRecommendationList.animate({
            height: (response.users.length * 70) + 'px'
        }, 500, function() {
        });
        var lastSuggestion;

        for (var i = 0, len = response.users.length; i < len; i++) {
            lastSuggestion = newUserSuggestion(response.users[i]);
        }

        if (lastSuggestion) {
            lastSuggestion.addClass('last');
        }
    }

    function newUserSuggestion(user) {
        var newUserSuggestion = userRecommendationSkeleton.clone(true);
        newUserSuggestion.find('span.UserName').text(user.firstName + ' ' + user.lastName);
        newUserSuggestion.find('a.UserLink').attr('href', '/profile/' + user.userId);
        newUserSuggestion.find('img').attr('src', '/photo/' + user.userId + '/Medium');

        if (user.Location) {
            newUserSuggestion.find('span.UserLocation').text(user.Location);
        }

        if (user.College) {
            newUserSuggestion.find('span.UserCollege').text(user.College);
        }
        else {
            newUserSuggestion.find('span.UserCollege').parent( ).hide( );
        }

        newUserSuggestion.find('span.UserAge').text(user.age);
        newUserSuggestion.find('button.GetSocialButton').text('Get Social')
                .bind('click', function( ) {
            FriendRequestManager.showPopup({name: user.firstName, userId: user.userId});
        });
        newUserSuggestion.appendTo(userRecommendationList);
        newUserSuggestion.removeClass('hide');
        return newUserSuggestion;
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

            Main.fetchFromServer('/user/' + $(this).attr('data-user-id'), {fields: 'firstName,lastName'}, function(response) {
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

    init();
};

$(function() {
    new FriendNetwork( );
});
