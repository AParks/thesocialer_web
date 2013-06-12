<?php
if (get_magic_quotes_gpc()) {
  $process = array(&$_GET, &$_POST, &$_COOKIE, &$_REQUEST);
  while (list($key, $val) = each($process)) {
    foreach ($val as $k => $v) {
      unset($process[$key][$k]);
      if (is_array($v)) {
	$process[$key][stripslashes($k)] = $v;
	$process[] = &$process[$key][stripslashes($k)];
      } else {
	$process[$key][stripslashes($k)] = stripslashes($v);
      }
    }
  }
  unset($process);
 }

session_start( );

$requestURI = $_SERVER['REQUEST_URI'];

$request = parse_url( $requestURI );
$_GET['x'] = '1';
if ( $_GET['x'] === '1' ) {
  $_SESSION['good'] = true;
}

if ( $_SESSION['good'] !== true ) {
  return;
}

require( 'AutoLoader.php' );

function getNode( $path, $config, & $remainder = array( ) ) {
  $node = $config->xpath( '//site/entry[@uri="/' . $path .'"]' );
  if ( ! $node ) {
    $pieces = explode( '/', $path );
    array_unshift( $remainder, array_pop( $pieces ) );
    
    if ( $pieces ) {
      $node = getNode( implode( '/', $pieces ), $config, $remainder );
    }
  }

  return $node;
}

$site = simplexml_load_file( 'Configuration/site.xml' );
$requestValues = array( );
$node = getNode( trim( $request['path'], '/'), $site, $requestValues );//$site->xpath( '//site/entry[@uri="' . $request['path'] .'"]' );

if (count($node) > 0)
  $class = trim( (string) $node[0] );
else $class = '';

if ( $class === '' || class_exists( $class ) === false ) {
  header("HTTP/1.1 404 Not Found");
  echo "request uri: $requestURI";
  echo "<br> error code: 404";
  exit;
}

$application = new $class( );
$application->setRequestValues( $requestValues );
$application->execute( );
DisplayManager::getInstance( )->output( );
