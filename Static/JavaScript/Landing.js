
$(function( ) {

    var height = $('#container').height();
    $('#container-black').height(height);
    $('.community').height($('.community').width());
    
    window.onresize = function() {
        var height = $('#container').height();
        $('#container-black').height(height);
        $('.community').height($('.community').width());
    };
    $(window).scroll(function() {
        var pos = $(window).scrollTop();
        if (pos < 62) 
            $('#Navigation').css('position', 'relative');
        else
            $('#Navigation').css('position', 'fixed');

    });
   
    mixpanel.track_links('a.host-now', "'Host now' button click", { "user_id": Viewer.userId});
    mixpanel.track_links('a.create-popup', "'Create a new popup' button click- landing page", {"user_id": Viewer.userId});
    mixpanel.track_links('a.discover-now', "'Discover now' button click", { "user_id": Viewer.userId});



    $('#myCarousel').carousel({
        interval: 4000
    });
});