_FriendRequestManager = function( ){

  var $popup;

  this.showPopup = function( userData ){
    dismiss( );

    var markup = '<div id="FriendRequestPopup">'
               +   '<div class="inner">'
               +     'Ask ' + userData.name + ' to get social?<br />'
               +     '<button class="Blue standard">Yes</button>'
               +     '<button class="Gray standard">No</button>'
               +   '</div>'
               + '</div>';

    $popup = $(markup).appendTo('body');
    Main.centerObject( $popup );
    $popup.find('button:last').bind('click', dismiss);
    $popup.find('button:first').bind('click', function( ){ requestFriendship( userData ); } );
  };

  function dismiss( )
  {
    if ( $popup )
    {
      $popup.remove( );
      delete $popup;
    }
  }

  function requestFriendship( userData )
  {
    Main.fetchFromServer( '/network/json/request', { userId: userData.userId }, requestFriendshipResponse );
  }

  function requestFriendshipResponse( response )
  {
    var markup = '';

    if ( response.success )
    {
      markup = 'Request sent!<br /><button class="Blue standard">OK</button>';  
    }
    else
    {
      markup = 'You\'ve already asked them to get social.<br /><button class="Blue standard">OK</button>';
    }

    $popup.find('div.inner').html( markup );
    $popup.find('button').bind('click', dismiss);
  }
};

FriendRequestManager = new _FriendRequestManager( );
