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

    $('.EventPrice').bind('click', function() {
        /* mixpanel.track("Bookmarked", {
         "userId": Viewer.userId,
         "Event Title": $(this).prev()
         });*/
        if (confirm("Are you sure you want to delete this event? This can't be undone.")) {
            $.ajax({
                url: 'featured/json',
                type: 'POST',
                data: {action: 'delete', eventId: $(this).attr('data-event-id')},
                success: function(data) {
                    location.reload(true);
                }
            });
        }
        return false;
    });
    $('.notLoggedIn').bind('click', function() {
        $('#myModal').modal('show');
    });
    $('#join-email').bind('click', function() {
        $('#myModal2').modal('show');

    });


});