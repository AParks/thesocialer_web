/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */


$(function( ) {
    
    filepicker.setKey('ATkirnHVuRJyx5NEogt6gz');
    $('#image-upload').bind('click', function(){
    filepicker.pickMultiple(
            {
                mimetypes: 'image/*',
                container: 'window'
            },
            function(InkBlobs) {
                
                for(i=0; i< InkBlobs.length; i++)
                    console.log(InkBlobs[i].url);
                
                $.ajax({
                url: '/community',
                type: 'POST',
                data: {
                    photos: InkBlobs
                },
                success: function(data) {
                    //console.log(data);
                    location.reload(true);
                }
            });
            });
    });

});