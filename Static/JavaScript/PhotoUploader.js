PhotoUploader = function( ) {

    var $container;
    var jcrop_api, boundx, boundy;



    this.start = function( )
    {
        $container = $('.PhotoUploader');
        $container.css('display', 'inline-block');
        $container.find('a.PhotoUploader_Close').bind('click', function( ) {
            $container.css('display', 'none');
            return false;
        });

       

        var iframe = $('<iframe id="PhotoUploadForm" name="PhotoUploadFrame" class="hide" />').appendTo('body');
        $container.find('form').get(0).target = 'PhotoUploadFrame';
        iframe.get(0).onload = function( ) {
            finished(iframe);
        };

        Main.centerObject($container);
    }

    function finished(iframe)
    {
        var f = frames['PhotoUploadFrame'].document.getElementsByTagName('body')[0];

        if (!f.innerHTML)
        {
            return;
        }
        response = eval('(' + f.innerHTML + ')');
        console.log(response);
        $('#PhotoUploadForm').remove();
        delete frames['PhotoUploadFrame'];
        $container.addClass('Step2');

        $container.find('form').remove();
        $container.append('<h2>Choose a thumbnail for this photo</h2>');
        $container.append('<img id="UploadedPhoto" src="/Photos/Large/' + response.file + '" />');
        $('#UploadedPhoto').Jcrop({
            onChange: updatePreview,
            onSelect: updatePreview,
            aspectRatio: 1
        }, function() {
            // Use the API to get the real image size
            var bounds = this.getBounds();
            boundx = bounds[0];
            boundy = bounds[1];
            // Store the API in the jcrop_api variable
            jcrop_api = this;
            this.setSelect([0, 0, $('#UploadedPhoto').width(), $('#UploadedPhoto').height()]);
        });


        $container.append('<div id="UploadedThumbnail"><img id="UploadedThumbnailImage" src="/Photos/Large/' + response.file + '" /></div>');
        $container.append('<div class="SaveThumbnailContainer"><button class="standard Blue">Save Thumbnail</button></div>');

        $container.find('button').click(function( ) {
            var scaled = jcrop_api.tellScaled( );
            saveThumbnail(response.photoId, scaled.x, scaled.y, scaled.w, scaled.h);
        });
    }

    function saveThumbnail(photoId, x, y, width, height)
    {
        var params = {photoId: photoId, x: x, y: y, width: width, height: height};

        $.ajax({
            url: '/photoManager/json/setThumbnail',
            type: 'POST',
            data: params,
            success: function(data)
            {
                var $success = $('<div class="SuccessMessage">Thumbnail Saved!<br /><a class="Close" href="#">Close</a></div>');
                $success.find('a.Close').bind('click', function( ) {
                    $container.remove();
                    return false;
                });

                $container.append($success);
                location.reload(true);
            }
        });
    }

    function updatePreview(c)
    {
        if (parseInt(c.w) > 0)
        {
            var rx = 106 / c.w;
            var ry = 106 / c.h;

            $('#UploadedThumbnailImage').css({
                width: Math.round(rx * boundx) + 'px',
                height: Math.round(ry * boundy) + 'px',
                marginLeft: '-' + Math.round(rx * c.x) + 'px',
                marginTop: '-' + Math.round(ry * c.y) + 'px'
            });
        }
    }
    ;
};
