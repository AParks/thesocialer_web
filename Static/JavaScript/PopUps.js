/*
 * anna parks 
 */


$(function( ) {


    var height = $('#container').height();
    $('#container-black').height(height);
    window.onresize = function() {
        var height = $('#container').height();
        $('#container-black').height(height);
    };

    $('.EventPrice.logged_in').bind('click', function() {
        mixpanel.track("Bookmarked", {
            "userId": Viewer.userId,
            "Event Title": $(this).prev()
        });
    });
    $('.notLoggedIn').bind('click', function() {
        $('#myModal').modal('show');
    });
    $('#join-email').bind('click', function() {
        $('#myModal2').modal('show');

    });


});