var UserPhotos = function( )
{
  var $photos = $('.UserPhoto');
  var photoCount = $photos.length;
  var $popup;
  var $active;

  function init( )
  {
    $photos.bind('click', showPhoto);  
  }

  function showPhoto( )
  {
    $active = $(this);
    var markup = '<div id="PhotoPopup">'
               +   '<div class="inner">'
               +     ( photoCount ? '<a href="#" id="PhotoPopup_Prev">Previous</a>' : '' )
               +     '<img style="max-width: 100%" src="' + $active.attr('data-large') + '" />'
               +     ( photoCount ? '<a href="#" id="PhotoPopup_Next">Next</a>' : '' )
               +   '</div>'
               + '</div>';

    $popup = $(markup).appendTo('body');
    Main.centerObject($popup);
    $popup.css( 'top', 50 +  $(window).scrollTop() + 'px' );
    var $overlay = Main.overlay();
    $overlay.bind('click', closePhoto);

    $( '#PhotoPopup_Prev' ).bind('click', function( ){
      $active = $active.prev();
      if ( $active.is('.UserPhoto') === false )
      {
        $active = $photos.filter(':last');
      }
      $popup.find('img').attr('src', $active.attr('data-large'));
      return false;
    });

    $( '#PhotoPopup_Next' ).bind('click', function( ){
      $active = $active.next();
      if ( $active.is('.UserPhoto') === false )
      {
        $active = $photos.filter(':first');
      }
      $popup.find('img').attr('src', $active.attr('data-large'));
      return false;
    });
  }

  function closePhoto( )
  {
    $popup.remove( );
    $popup = null;
    Main.removeOverlay( );
  }

  init( );
}

$(function(){
  userPhotos = new UserPhotos( );
});
