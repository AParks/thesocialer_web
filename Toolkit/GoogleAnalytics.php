<?php

class GoogleAnalytics extends ATransformableObject
{
  const ENVIRONMENT_DEV = 'dev';
  const ENVIRONMENT_PRODUCTION = 'production';

  protected $publicProperties = array( 'code' );
  protected $code = '';

  public function __construct( $environment = self::ENVIRONMENT_PRODUCTION )
  {
    $this->code = $this->_setCode( $environment );
  }

  public function getCode( )
  {
    return $this->code;
  }

  private function _setCode( $environment )
  {
    if ( $environment === self::ENVIRONMENT_DEV )
    {
      return
<<<CODE
<script type="text/javascript">

  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', 'UA-32668103-1']);
  _gaq.push(['_trackPageview']);

  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();

</script>
CODE;
    }

    return
<<<CODE
<script type="text/javascript">

  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', 'UA-32668103-1']);
  _gaq.push(['_trackPageview']);

  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();

</script>
CODE;
  }
}
