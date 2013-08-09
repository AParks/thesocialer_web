/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */


$(function( ) {
    
    filepicker.setKey('ATkirnHVuRJyx5NEogt6gz');
  //  $('.popup_photo').bind('click', function(){
  //      console.log($(this).attr('url'));
  //      $('.modal-photo img').attr('src', $(this).attr('url'));
  //      $('.modal-photo').modal('show');
  //  });
    $('#image-upload').bind('click', function(){
    filepicker.pickMultiple(
            {
                mimetypes: 'image/*',
                container: 'window'
            },
            function(InkBlobs) {
                
                
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