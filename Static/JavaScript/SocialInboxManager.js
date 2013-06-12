function _SocialInboxManager( ){

  var _this = this;

  this.acceptNotification = function( notification )
  {
    Main.fetchFromServer('/network/json/accept', { userId: $(notification).attr('data-sender') });
    _this.dismissNotification(notification); 
  }

  this.declineNotification = function( notification )
  {
    Main.fetchFromServer('/network/json/decline', { userId: $(notification).attr('data-sender') });
    _this.dismissNotification(notification);
  }
  
  this.dismissShareNotification = function( notification )
  {
	// TODO THIS IS WRONG - you can't just remove using today's date
	  // need to remove using the date of the specific locationshare
	  //also loc isnt the right loc
	  // need to use this $(notification).attr('data-sender') with more attrs
	  // I think I fixed this, but i'll leave this comment here for now
    Main.fetchFromServer('/network/json/dismissshare', { locationId: $(notification).attr('data-loc-id'), locationDate: $(notification).attr('data-loc-date') });
    _this.dismissNotification(notification);
  }

  this.init( );
};

_SocialInboxManager.prototype.dismissNotification = function(notification)
{
  var _this = this;
  
  $(notification).slideUp('fast', function( ){
    $(this).remove( );

    _this.notifications = _this.$notificationContainer.find( 'li.Notification' ).not('.None');
    _this.setCount( _this.notifications.length );
    
    if ( _this.notifications.length === 0 )
    {
      _this.$notificationContainer.hide( );
    }
  });
}

_SocialInboxManager.prototype.bindButtons = function(notification)
{
  var _this = this;
  $(notification).find('button.accept').bind('click', function( ){
    _this.acceptNotification(notification);
  });
  $(notification).find('button.decline').bind('click', function( ){
    _this.declineNotification(notification);
  });
  $(notification).find('button.dismiss').bind('click', function( ){
	_this.dismissShareNotification(notification);
  });
}

_SocialInboxManager.prototype.init = function( )
{
  var _this = this;
  this.$obj = $('#SocialInbox');
  this.$notificationContainer = $('#SocialInboxNotifications');

  this.$obj.find( 'div.clickRegion').bind('click', function(){
    _this.$notificationContainer.toggle();
    return false;
  });

  this.notifications = this.$notificationContainer.find( 'li.Notification' );

  this.notifications.each(function( ){
    _this.bindButtons($(this));
  });
}

_SocialInboxManager.prototype.setCount = function( cnt )
{
  $('#SocialInboxCount').text( cnt );
}


var SocialInboxManager = new _SocialInboxManager( );
