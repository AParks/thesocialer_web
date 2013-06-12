EventInput = function( )
{
  var obj = $( '#EventInput' );
  var submit = $( '#EventSubmit' );

  var defaultText = obj.val( );

  obj.bind( 'focus', function( ){
    if ( obj.val( ) === defaultText )
    {
      obj.val( '' ).removeClass( 'inactive' );
    }
  } ).bind( 'blur', function( ){
    if ( obj.val( ) === '' )
    {
      obj.val( defaultText ).addClass( 'inactive' );
    }
  } );

  submit.bind( 'click', function( ){
    Search.submit( obj.val( ), function( d ){ console.log( d ); } );
  } );
};

Search = 
{
  submit: function( query, callback )
  {
    $.ajax( {
      url: '/json/search',
      type: 'GET',
      data: { query: query },
      success: function( data )
      {
        callback( data );
      }
    } );
  }
}

$( function( ){
  new EventInput( );
} );
