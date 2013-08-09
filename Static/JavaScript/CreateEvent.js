
function validateForm() {
    var isValid = true;
    if ($('#last').find('.error').length == 0) {

        // Check that fields aren't empty
        if ($('#file').val() == "") {
            $('.add-photo').next().next().text(" No file chosen");
            isValid = false;
        }
        if ($('input[name=price]').val() == "") {
            $('input[name=price]').addClass('error');
            isValid = false;

        }
        
        //input is not of the form "20.00" and is not the form "0"
        if (!($('input[name=price]').val().match(/^\d+(\.\d{2})$/)) &&
            !($('input[name=price]').val().match(/^0+$/)) ) {
            $('input[name=price]').next().text(" Invalid price. Must look like the form '20.00'");
            isValid = false;
        }
    }
    else
        isValid = false;


    if (isValid) {
        /*  var startTime = $('input[name=startTime]').val();
         var date = $('input[name=startDate]').datepicker('getDate');
         var startDate = (date.getMonth() + 1) + '/' + date.getDate() + '/' + date.getFullYear() ;
         var fullStartDate = startDate + " " + startTime;
         $('input[name=startDate]').val(fullStartDate);
         
         var endTime = $('input[name=endTime]').val();
         var d = $('input[name=endDate]').datepicker('getDate');
         var endDate = (d.getMonth() + 1) + '/' + d.getDate() + '/' + d.getFullYear() ;
         var fullEndDate = endDate + " " + endTime;
         $('input[name=startDate]').val(fullEndDate);
         */
        setUpDate($('input[name=startDate]'), $('input[name=startTime]'));
        setUpDate($('input[name=endDate]'), $('input[name=endTime]'));
    }

    return isValid;
}
function setUpDate(date, time) {
    var time_val = time.val();
    var d = date.datepicker('getDate');
    var formatted_date = (d.getMonth() + 1) + '/' + d.getDate() + '/' + d.getFullYear();
    var fullDate = formatted_date + " " + time_val;
    date.val(fullDate);
}
function setTime($time, hours) {

    var min = $time.getMinutes();
    var merid = ' am';
    if (hours >= 12 && hours < 24)
        merid = ' pm';

    if (hours > 12 || hours === 0) {
        hours = hours % 12;
        if (hours === 0)
            hours = '12';
    }
    else if (hours < 10)
        hours = '0' + hours;

    if (min < 10)
        min = '0' + min;
    return hours + ":" + min + merid;
}


function errorClass($input) {
    $input.keyup(function() {

        if ($(this).val() == "")
            $(this).addClass('error');
        else
            $(this).removeClass('error');
    });
}
function validate($input) {
    if ($input.val() == "") {
        $input.addClass('error');
        return false;
    } else
        $input.removeClass('error');
    return true;
}


$(function() {

    $('input[name=price]').focusout(function() {
        var price = $(this).val();
        if ($(this).val().match(/^\d+$/)) {
            $(this).val(price + '.00');
        }
        if (($('input[name=price]').val().match(/^\d+(\.\d{2})$/) ||
                $('input[name=price]').val().match(/^0+$/))) {
            $(this).next().text("");
            $(this).removeClass('error');
        }
        else {
            $(this).addClass('error');
            $(this).next().text(" Invalid price. Must look like the form '20.00'");
        }


    });
    $('.add-photo').bind('click', function() {
        $("input[type=file]").click();
    });
    $('input[type=file]').change(function(e){
        var files = $(this)[0].files;
        $('.add-photo').next().text("");
        
        for(var i = 0; i< files.length; i++){
            if(!files[i].type.match(/^image/)){
                $('.add-photo').next().addClass('error');
                $('.add-photo').next().text('Not an image!');
                return;
            }
            $('.add-photo').next().append(files[i].name + ", ");
        }
        if(files.length > 0){
            $('.add-photo').next().removeClass('error');
            var old =  $('.add-photo').next().text();
            $('.add-photo').next().text(old.substring(0, old.length - 2));
        }
        else{
            $('.add-photo').next().addClass('error');
            $('.add-photo').next().text('No file chosen');
        }
        
    });
    $('textarea').autosize();

    $('#end_time').bind('click', function() {
        var start_date = $('input[name=startDate]').val();
        var start_time = $('input[name=startTime]');
        var start = new Date(start_date + " " + start_time.val());
        if (isNaN(start.getTime()))
            start_time.addClass('error');
        else {
            $('span#to').css('display', 'inline-block');

            $(this).css('display', 'none');
            $('input[name=endDate]').css('display', 'inline-block').val(start_date);
            $('input[name=endTime]').css('display', 'inline-block');

            var time = setTime(start, start.getHours() + 3);
            $('input[name=endTime]').val(time);
        }

    });
    $(".datepicker").datepicker({
        minDate: 0,
        dateFormat: "mm/dd/yy"});
    $('input[name=startDate]').change(function() {
        var date = $(this).datepicker('getDate');
        $("input[name=endDate]").datepicker("option", "minDate", date);
    });
    $(".datepicker").keyup(function(event) {
        var c = String.fromCharCode(event.keyCode);
        var isDigit = c.match(/\d/);
        var date = $(this).val();
        if (isDigit && date.match(/^\d+\/\d+\/\d+$/))
            $(this).datepicker("setDate", date);
    });
    var date = new Date();
    var cur = (date.getMonth() + 1) + '/' + date.getDate() + '/' + date.getFullYear();
    $("input[name=startDate]").val(cur);
    $("input[name=startTime]").val('06:00 pm');
    $("input[name=endDate]").val(cur);

    $(".time").focusout(function() {
        var start_date = $(this).prev().prev().val();
        var start = new Date(start_date + " " + $(this).val());
        if (isNaN(start.getTime()))
            $(this).addClass('error');
        else {
            $(this).removeClass('error');
            var time = setTime(start, start.getHours());
            $(this).val(time);

        }
    });


    $('.required').each(function() {
        errorClass($(this));
    });
    $('#next').bind('click', function() {
        $current = $('fieldset[class=active]');
        $current.find('.required').each(function() {
            validate($(this));
        });


        if ($current.find('.error').length == 0) {
            $next = $current.removeClass('active').addClass('inactive').next();
            $next.removeClass('inactive').addClass('active');

            $next_icon = $('.step-container.active').removeClass('active').addClass('inactive').next();
            $next_icon.addClass('active');
            $('.butn').css('display', 'inline-block');

            if ($next.attr('id') === 'last') {
                $('#next').css('display', 'none');
                $('input.new_popup_button[type=submit]').css('display', 'inline-block');
            }

        }
    });


    $('.butn').bind('click', function() {
        $('input.new_popup_button[type=submit]').css('display', 'none');
        $('#next').css('display', 'inline-block');


        $prev_icon = $('.step-container.active').removeClass('active').addClass('inactive').prev();
        $prev_icon.addClass('active');

        $prev = $('fieldset[class=active]').removeClass('active').addClass('inactive').prev();
        $prev.removeClass('inactive').addClass('active');

        if ($prev.attr('id') === 'first') {
            $(this).css('display', 'none');
        }
    });
    var options = {
        appendTo: "#popup-what",
        source: function(request, response) {
            var matches = $.map(times, function(acItem) {
                var index = acItem.toUpperCase().indexOf(request.term.toUpperCase());
                var beginning = (index === 0) || (index === 1 && acItem.indexOf('0') === 0);
                if (request.term.length < 4 && acItem.indexOf('am') !== -1) {
                    if (beginning)
                        return acItem;
                } else if (request.term.length >= 4) {
                    if (beginning)
                        return acItem;
                }
            });
            response(matches);
        }
    };
    $(".time").autocomplete(options);




});