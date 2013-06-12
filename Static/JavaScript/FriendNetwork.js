var FriendNetwork = function() {
    var activeDate = formatDate(new Date());
    
    var userRecommendationList = $('ul.UserRecommendationList');
    var userRecommendationSkeleton = userRecommendationList.find('li.UserRecommendationSkeleton').removeClass('UserRecommendationSkeleton').remove();
    
    function init() {
	loadUserRecommendations();
    }
    
    function loadUserRecommendations() {
	Main.fetchFromServer('/recommendations/user/json/get', { limit: 10 }, receiveUserSuggestions);
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
	if(response.users.length == 0) {
	    userRecommendationList.append("<p>No recommendations for you.</p>");
	    userRecommendationList.animate({
		height: '50px'
	    }, 200, function() {
	    });
	    $(".SeeMoreButton").remove();
	    return;
	}
	userRecommendationList.animate({
	    height: (response.users.length*70)+'px'
	}, 500, function() {
	});
	var lastSuggestion;
	
	for ( var i = 0, len = response.users.length; i < len; i++ ) {
	    lastSuggestion = newUserSuggestion(response.users[i]); 
	}
	
	if(lastSuggestion) {
	    lastSuggestion.addClass('last');
	}
    }
    
    function newUserSuggestion(user) {
	var newUserSuggestion = userRecommendationSkeleton.clone(true);
	newUserSuggestion.find('span.UserName').text(user.firstName + ' ' + user.lastName);
	newUserSuggestion.find('a.UserLink').attr('href', '/profile/' + user.userId);
	newUserSuggestion.find('img').attr('src','/photo/' + user.userId + '/Medium');
	
	if ( user.Location ) {
	    newUserSuggestion.find('span.UserLocation').text(user.Location);
	}
	
	if ( user.College ) {
	    newUserSuggestion.find('span.UserCollege').text(user.College);
	}
	else {
	    newUserSuggestion.find('span.UserCollege').parent( ).hide( );
	}
	
	newUserSuggestion.find('span.UserAge').text(user.age);
	newUserSuggestion.find('button.GetSocialButton').text('Get Social')
            .bind('click', function( ){ FriendRequestManager.showPopup({name: user.firstName, userId: user.userId}); });
	newUserSuggestion.appendTo(userRecommendationList);
	newUserSuggestion.removeClass('hide');
	return newUserSuggestion;
    }
    
    function hoverOverMember( ) {
	var container = $(this);
	var memberName = container.find('div.MemberName');
	if ( memberName.length ) {
	    memberName.show( );
	}
	else {
	    var nameDiv = $('<div class="MemberName"></div>');
	    nameDiv.hide().addClass('mouseOver');
	    container.append(nameDiv);
	    
	    Main.fetchFromServer('/user/' + $(this).attr('data-user-id'), { fields: 'firstName,lastName' }, function( response ){
		nameDiv.text(response.firstName + ' ' + response.lastName);
		nameDiv.css('left', -1 * ( nameDiv.outerWidth( ) - container.outerWidth( ) ) / 2 );
		if(nameDiv.hasClass('mouseOver')) {
		    nameDiv.show();
		}
	    } );
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

$(function(){
  //  new FriendNetwork( );
});
