var Search = function(){
    var $searchField = $('#SearchField');
    var $searchButton = $('#SearchButton');
    var $searchForm = $('#SearchForm');
    var activeDate = new Date();
    var activeDateText = formatDate(activeDate);
    var placeholder1 = "Search by name, email, or college";
    var placeholder2 = "Search by name";
    
    function init() {
	$searchField.bind('focus', function(){
	    
	    if ( $searchField.val() === placeholder1 || $searchField.val() === placeholder2 ) {
		$searchField.val('');
	    }
	    $searchField.removeClass('default');
	});
	
	$searchField.bind('blur', function(){
	    if ( $searchField.val() === '' ) {
		$searchField.val($searchField.get(0).defaultValue);
		$searchField.addClass('default');
	    }
	});
	
	$searchForm.bind('submit', function(){
	    if ( $searchField.val() === '' || $searchField.val() === $searchField.get(0).defaultValue ) {
		$searchField.focus();
		return false;
	    }
	    search($searchField.val());
	    return false;
	});
	$('.DateText').text("on "+(new Date(activeDateText)).toString().slice(0,10));
	$('.ChangeDateButton').click(function(){
	    activeDate.setDate(activeDate.getDate()+1);
	    activeDateText = formatDate(activeDate);
	    $('.DateText').text("on "+activeDate.toString().slice(0,10));
	});
	
	$('#places').bind('click', function() {
    	    if (! $(this).hasClass('active')) {
    		$(this).addClass('active');
    		$('#people').removeClass('active');
    		$searchField.val('Search by name');
		$('.DateInfo').show();
    	    }
	});
	
	$('#people').bind('click', function() {
    	    if (! $(this).hasClass('active')) {
    		$(this).addClass('active');
    		$('#places').removeClass('active');
    		$searchField.val('Search by name, email, or college');
		$('.DateInfo').hide();
    	    }
	});
	
//	loadUserRecommendations();
    }
    
    function search(query) {
	$('#Results div.SearchResult').remove();
	if ($('#people').hasClass('active')) {
    	    Main.fetchFromServer('/search/json/searchuser', {query: query}, receiveUserResponse);
	}
	else {
    	    Main.fetchFromServer('/search/json/searchlocation', {query: query}, receiveLocationResponse);
	}
    }
    
    function receiveUserResponse(response) {
	for ( var i = 0, len = response.results.length; i < len; i++ ) {
	    $('#Results').append(getUserMarkup(response.results[i]));
	}
    }
    
    function getUserMarkup(user) {
	var markup = '<div class="SearchResult">'
            +   '<a href="/profile/' + user.userId + '"><img src="/photo/' + user.userId + '/Small" /></a>'
            +   '<div class="Name"><a href="/profile/' + user.userId + '">' + user.firstName + ' ' + user.lastName + '</a>, ' + user.age + '</div>'
            +   ( user.College ? '<div>' + user.College + '</div>' : '' )
            +   ( user.Location ? '<div>' + user.Location + '</div>' : '' )
            +   '<div class="clear"></div>'
            + '</div>';
	return markup;
    }
    
    function receiveLocationResponse(response) {
	//console.log(response);
	for ( var i = 0, len = response.results.length; i < len; i++ ) {
	    $('#Results').append(getLocationMarkup(response.results[i]));
	}
    }
    
    function getLocationMarkup(loc) {
	var markup = '<div class="SearchResult">'
            +   '<a href="/location/' + loc.id + '/'+activeDateText+'"><img class="LocImage" src="/Photos/Locations/' + loc.image + '" /></a>'
            +   '<div class="Name"><a href="/location/' + loc.id + '/' + activeDateText +'">' + loc.name + '</a></div>'
        /*+   ( user.College ? '<div>' + user.College + '</div>' : '' )
          +   ( user.Location ? '<div>' + user.Location + '</div>' : '' )*/
            +   '<div class="clear"></div>'
            + '</div>';
	return markup;
    }
    
    var userRecommendationList = $('ul.UserRecommendation_List');
    var userRecommendationSkeleton = userRecommendationList.find('li.UserRecommendationSkeleton').removeClass('UserRecommendationSkeleton').remove();
    
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
	userRecommendationList.animate({
	    height: '702px'
	}, 500, function() {
	});
	var lastSuggestion;
	
	for ( var i = 0, len = response.users.length; i < len; i++ ) {
	    lastSuggestion = newUserSuggestion(response.users[i]); 
	}
	
	lastSuggestion.addClass('last');
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
    
    function formatDate(dateObject) {
	return dateObject.getFullYear() + '-' + (dateObject.getMonth() + 1) + '-' + dateObject.getDate();
    }
    
    init();
};

$(function(){
    search = new Search();
});
