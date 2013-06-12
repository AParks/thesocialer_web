_EventSharer = function( ){
    var _this = this;
    var $popup;
    var $friends;
    var $allusers;
    var selectedUsers = new Array();
    
    
    this.showFriendSelector = function( ) {
	Main.fetchFromServer('/network/json/get', {}, receiveFriends);
	Main.fetchFromServer('/network/json/getall', {}, receiveAllUsers);
    };
    
    function receiveFriends(friends) {
	$friends = friends;
	createPopup();
	displayInPopup($friends);
    }
    
    function receiveAllUsers(allusers) {
	$allusers = allusers;
    }
  
    function createPopup() {
	var markup = '<div id="EventSharerPopup">'
	    + '<div class="CloseWindow">&#10006;</div>'
            +   '<div class="inner">'
            +     '<h1>Select people to invite</h1>'
            + 	 '<a class="usersel" id="all">All</a> | <a class="usersel" id="friends">Friends</a>'
            +     '<ul>'
            +     '</ul>'
            +     '<button id="sendshare" class="Blue standard">Send</button>'
            +   '</div>'
            + '</div>';
	
	
	$popup = $(markup).appendTo('body');
	if( Viewer['userId'] == 193 ) {
	    $popup.append('<button id="shareall" class="Blue standard">Share All (Admin)</button>');
	}
	Main.centerObject( $popup );
	var $overlay = Main.overlay();
	$overlay.bind('click', closePopup);
	$('.CloseWindow').bind('click', closePopup);
	
	$('#all').bind('click', function(){
    	    displayInPopup($allusers);
  	});
	$('#friends').bind('click', function(){
    	    displayInPopup($friends);
  	});
	
	$('#sendshare').bind('click', function(){
    	    sendShare();
    	    closePopup();
  	});
	$('#shareall').bind('click', function(){
    	    Main.fetchFromServer( '/network/json/shareall', { locationId: loc.id, locationDate: date }, null );
    	    closePopup();
	    location.reload(true);
  	});
    }
    
    function closePopup() {
	$popup.remove( );
	$popup = null;
	Main.removeOverlay( );
    }
    
    function displayInPopup(users) {
	var $list = $popup.find('ul');
	$list.html("");
	
	for ( var i = 0, len = users.friends.length; i < len; i++ ) {
	    $list.append(getMarkup(users.friends[i]));
	}
	
	$list.find('li').bind('click', function(){
	    selectUser($(this));
	});
	$list.append('<li class="clear"></li>');
	var id;
	for (id in selectedUsers) {
	    if(selectedUsers[id] == true) {
		$('#'+id).toggleClass('selected');
	    }
	}
    }
    
    function selectUser(which) {
	var id = which.attr('id');
	var isSelected = selectedUsers[id];
	//alert(isSelected);
	if ( isSelected == null ) {
	    selectedUsers[id]=true;
	}
	else {
	    selectedUsers[id] = !isSelected;
	}
	which.toggleClass('selected');
    }
    
    function getMarkup(friend) {
	return '<li class="Friend" id="'+friend.userId+'">'
            +   '<img src="/photo/' + friend.userId + '/Small" />'
            +   '<div>' + friend.firstName + ' ' + friend.lastName + '</div>'
            + '</li>'
    }
    
    function sendShare( ) {
	for (user in selectedUsers) {
	    if(selectedUsers[user]) {
		//alert("Sending to user "+user);
		Main.fetchFromServer( '/network/json/share', { userId: user, locationId: loc.id, locationDate: date }, null );
	    }
	}
	markup = 'Sent!<br /><button class="Blue standard">OK</button>';
	$popup.find('div.inner').html( markup );
	$popup.find('button').bind('click', dismiss);
    }
    
    function sendShareResponse( response ) {
	var markup = '';
	//alert(response.e);
	if ( response.success ) {
	    markup = 'Sent!<br /><button class="Blue standard">OK</button>';  
	}
	else {
	    markup = 'Error.<br /><button class="Blue standard">OK</button>';
	}
	$popup.find('div.inner').html( markup );
	$popup.find('button').bind('click', dismiss);
    }
    
    function dismiss( ) {
	if ( $popup ) {
	    $popup.remove( );
	    delete $popup;
	}
    }
    
};

eventSharer = new _EventSharer( );
