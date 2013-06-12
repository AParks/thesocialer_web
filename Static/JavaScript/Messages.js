var Messages = function( ){

  function init( ) {
      $('#Messages li').bind('click', function( ){
	  top.location = '/messages/thread/' + $(this).attr('thread-id');
      });
  }
    init( );
};

new Messages( );
