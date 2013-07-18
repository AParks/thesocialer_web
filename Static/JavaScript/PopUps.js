/*
 * anna parks 
 */


$(function( ) {
    $('.EventPrice').tooltip();

    $('.EventPrice').bind('click', function() {
        mixpanel.track("Bookmarked", {
            "userId": Viewer.userId,
            "Event Title": $(this).prev()
        });
    });
});