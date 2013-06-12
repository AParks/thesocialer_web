AdminLocationManager = { };

AdminLocationManager.AddLocationForm = { };

AdminLocationManager.AddLocationForm.init = function( ){
  $('button', '#AddLocationForm').click(function( ){
    AdminLocationManager.AddLocationForm.submit( );
  });
  
  $('a.delete', '#Locations').click(function( ){
    var $row = $(this).parent( ).parent( );
    AdminLocationManager.deleteLocation( $row.find( 'td:first' ).html( ),
      function( success )
      {
        if ( success )
        {
          $row.remove( );
        }
        else
        {
          $row.after( '<tr><td colspan="7">Failed to remove location.</td></tr>' );
        }
      });
    return false;
  });
};

AdminLocationManager.deleteLocation = function( locationId, callback )
{
  $.ajax( {
    url: '/admin/locations/json/delete',
    type: 'GET',
    data: { id: locationId },
    success: function( data )
    {
      callback( data.result );
    }
  } );
}

AdminLocationManager.AddLocationForm.submit = function( )
{
  var locationDetails = {
    name: $('input[name=name]').val( ),
    streetAddress: $('input[name=streetAddress]').val( ),
    description: $('textarea[name=description]').val( ),
    website: $('input[name=website]').val( ),
    yelpId: $('input[name=yelpId]').val( )
  };

  $.ajax( {
    url: '/admin/locations/json/add',
    type: 'GET',
    data: locationDetails,
    success: function( data )
    {
      if ( data.result === false )
      {
        var error = data.error ? data.error : 'Failed for unknown reason.';
        $( 'td.AddResult', '#AddLocationForm' ).parent( ).remove( );
        $( 'tr:last', '#AddLocationForm').after( '<tr><td class="AddResult" colspan="2">' + error + '</td></tr>' );
      }
      else
      {
        $( 'td.AddResult', '#AddLocationForm' ).parent( ).remove( );
        $( 'tr:last', '#AddLocationForm').after( '<tr><td class="AddResult" colspan="2">Success</td></tr>' );
        var markup = '<tr>'
                   + '<td>' + data.id + '</td>'
                   + '<td>' + data.name + '</td>'
                   + '<td>' + data.streetAddress + '</td>'
                   + '<td>' + data.description + '</td>'
                   + '<td>' + data.website + '</td>'
                   + '<td>' + data.yelpId + '</td>'
                   + '</tr>';

        $( 'tr:last', '#Locations' ).after( markup );
      }
    }
  } );
};

$(function(){
  AdminLocationManager.AddLocationForm.init();
})
