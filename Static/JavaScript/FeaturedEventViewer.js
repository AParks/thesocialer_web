FeaturedEventViewer = function(evt, userStatus) {

    var _this = this;
    var attendanceManager = new AttendanceManager( );
    var geocoder = new google.maps.Geocoder( );

    function init( ) {
    

        
        //add images to carousel
        var images = evt.markup.split(" ");
        evt.markup = images[1];
        for (i = 1; i < images.length; i++) {
            html = "<div class='item'>" +
                    "<img src='" + images[i] + "'/>" +
                    '</div>';
            $('.carousel-inner').append(html);
        }
        $('.carousel-inner').children().first().addClass('active');

        //calculate image margins
        $('div[class~=active] img').load(function() {
                calculateMarginsForImage($(this));
        });

        $('.carousel').on('slide', function() {

            arry = $('#FeaturedEvent img');
            for (i = 0; i < arry.length; i++) {
                var img = $('.carousel-inner').find(':nth-child(' + (i + 1) + ')[class~=item]');

                calculateMarginsForImage(img); 

            }
        });

    //facebook share
            $('a[id=share]').attr('href', "https://www.facebook.com/dialog/feed?"+
                    "app_id=327877070671041"+
                    "&link=https://developers.facebook.com/docs/reference/dialogs/"+
                    "&picture=https://thesocialer.com" + evt.markup+
                    "&name=" + evt.headline +  
                    "&caption=" + evt.sub_headline +  
                    "&description="+ evt.description + 
                    "&redirect_uri=https://thesocialer.com"+
                    "&display=popup");




        //show part of the event description initially, and 
        //show the rest when user clicks on see more
        $('#Description').text(evt.description.substring(0, 200));
        var n = evt.description.length;
        $('#SeeMore').click(function() {

            $('#SeeMore').css('display', 'none');
            $('#More_Description').text(evt.description.substring(200, n));
        });

        if (userStatus) {
            if (userStatus == "yes") {
                $('.AttendingCount').addClass('active');
            }
            else if (userStatus == "maybe") {
                $('.MaybeCount').addClass('active');
            }
        }
        var buttons = $('.AttendingCounts li');
        buttons
                .hover(
                function() {
                    $(this).addClass('hover');
                },
                function() {
                    $(this).removeClass('hover');
                }
        )
                .click(
                function() {
                    var numbox = $(this).find('strong');
                    // if this one was already active, deactivate it and set status to no
                    if ($(this).hasClass('active')) {
                        attendanceManager.setFeaturedEventAttendanceStatus(evt.featuredEventId, 'no');
                        $(this).removeClass('active');
                        numbox.text(parseInt(numbox.text()) - 1);
                        // remove photo from attending list
                        $('.Attendees').find("a[href='/profile/" + Viewer.userId + "']").remove();
                    }
                    // otherwise activate this one, and update the other numbers accordingly
                    else {
                        var activebox = $(this).parent().find('.active');
                        if (activebox) {
                            activenum = activebox.find('strong');
                            activenum.text(parseInt(activenum.text()) - 1);
                            activebox.removeClass('active');
                        }
                        attendanceManager.setFeaturedEventAttendanceStatus(evt.featuredEventId, $(this).attr('data-status'));
                        $(this).addClass('active');
                        numbox.text(parseInt(numbox.text()) + 1);
                        $('.Attendees').find("a[href='/profile/" + Viewer.userId + "']").remove();
                        var attendlist = ($(this).attr('data-status') == 'yes') ? $('.AttendeesYes') : $('.AttendeesMaybe');
                        attendlist.append('<a href="/profile/' + Viewer.userId + '"><img src="/photo/' + Viewer.userId + '/Small" class="UserPhoto" /> </a>');
                    }
                }
        );

        $("#FeaturedDescription").linkify();
        $("#FeaturedTime").text($('.FeaturedEventTime').text());

        if (Viewer.userId != -1) {
            $('.Attendees').find('a').hover(hoverOverMember, hoverOutMember);
        }

        _this.drawMap( );
    }

    function calculateMarginsForImage(img) {

        var width = img.width();
        var height = img.height();
        var marginLeft = parseInt(($('#myCarousel').width() - width) / 2);
        var marginTop = parseInt(($('#myCarousel').height() - height) / 2);
        if (marginLeft >= 0)
            img.css('margin-left', marginLeft);
        if (marginTop >= 0)
            img.css('margin-top', marginTop);
    }

    this.drawMap = function( ) {
        geocoder.geocode({'address': evt.location}, function(results, status) {
            if (status == google.maps.GeocoderStatus.OK) {
                map.setCenter(results[0].geometry.location);
                var marker = new google.maps.Marker({
                    map: map,
                    position: results[0].geometry.location
                });

                var link = '<div>' + evt.location + '</div>';
                var infowindow = new google.maps.InfoWindow({
                    content: link,
                    maxWidth: 100
                });
                google.maps.event.addListener(marker, 'click', function() {
                    infowindow.open(map, this);
                });
            }
        });
        var options = {
            zoom: 12,
            mapTypeId: google.maps.MapTypeId.ROADMAP
        };

        var map = new google.maps.Map(document.getElementById("LocationMap"), options);
    };

    function hoverOverMember(event) {
        var container = $(this);
        var memberName = container.find('div.MemberName');
        if (memberName.length) {
            memberName.show( );
        }
        else {
            var nameDiv = $('<div class="MemberName"></div>');
            nameDiv.hide().addClass('mouseOver');
            container.append(nameDiv);

            Main.fetchFromServer(
                    '/user/' + $(this).attr('data-user-id'),
                    {fields: 'firstName,lastName,College'},
            function(response) {
                nameDiv.html('<span class="UserName">' + response.firstName + '</span><br />' + '<span class="UserCollege">' + response.College + '</span>');
                $(document).mousemove(function(e) {
                    nameDiv.css({'top': e.pageY + 20, 'left': e.pageX - 10});
                });
                nameDiv.css({'top': event.pageY + 20, 'left': event.pageX - 10});
                //nameDiv.css('left', -1 * ( nameDiv.outerWidth( ) - container.outerWidth( ) )/2 );
                if (nameDiv.hasClass('mouseOver')) {
                    nameDiv.show();
                }
            });
        }
    }

    function hoverOutMember( ) {
        $(this).find('div.MemberName').hide( ).removeClass('mouseOver');
    }

    init( );
}

$(function( ) {



    $('#payButton').click(function() {
        if (Viewer.userId == -1) {
            $('#myModal').modal('show');
            return false;
        }
        else {
            var token = function(res) {
                var $input = $('<input type=hidden name=stripeToken />').val(res.id);
                var $id = $('<input type=hidden name=featured_event_id />').val(evt.featured_event_id);

                $('form[id=charge]').append($input).append($id).submit();
            };
//pk_test_tPl6A15XRwUWmiz0bEB280hN

            StripeCheckout.open({
                key: 'pk_live_lIxCZIgFwI8gw5HTSZ7nZrP7',
                address: true,
                amount: $('#checkout_total').val().replace(/\./g, ""),
                currency: 'usd',
                name: evt.headline,
                description: evt.sub_headline,
                panelLabel: 'Checkout',
                token: token
            });

            return false;
        }
    });
    calculatePrice();
    $('#charge').find('script').attr('data-description', evt.description);
    $('#charge').find('script').attr('data-name', evt.headline);

    $('select[type=number]').click(function() {
        calculatePrice();

    });

    function calculatePrice() {
        var price = parseFloat($('select[type=number]').val()) * parseFloat(evt.price);
        var fee = parseFloat(price * .029 + 0.30);
        var total = (price + fee).toFixed(2);
        $('#total_price').text(price.toFixed(2));


        //change fee amount
        $('#fee').text(fee.toFixed(2));

        //add the amount to the post value
        $('input[name=checkout_total]').remove();
        var $amount = $('<input id=checkout_total type=hidden name=checkout_total />').val(total);
        $('form[id=charge]').append($amount);



    }
    userStatus = 'yes';
    var fev = new FeaturedEventViewer(evt, userStatus);
});

