
$(function( ) {

    var height = $('#container').height();
    $('#container-black').height(height);
    window.onresize = function() {
        var height = $('#container').height();
        $('#container-black').height(height);
    };
    $(window).scroll(function() {
        var pos = $(window).scrollTop();
        if (pos < 62) 
            $('#Navigation').css('position', 'relative');
        else
            $('#Navigation').css('position', 'fixed');

    });


    $('#myCarousel').carousel({
        interval: 4000
    });
});