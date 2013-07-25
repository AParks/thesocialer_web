/*
 * anna parks 
 */


$(function( ) {
    //$('.EventPrice').tooltip();

    $('.EventPrice.logged_in').bind('click', function() {
        mixpanel.track("Bookmarked", {
            "userId": Viewer.userId,
            "Event Title": $(this).prev()
        });
    });
     $('.notLoggedIn').bind('click', function() {
                    $('#myModal').modal('show');
    });
});