<?php

class UserRecommendationsJSON extends JSONApplicationBase {
  const ACTION_GET = 'get';
  const PARAMETER_LIMIT = 'limit';

  const DEFAULT_LIMIT = 5;
  const MAX_LIMIT = 25;

  function execute( ) {
    switch ( $this->requestValues[0] ) {
    case self::ACTION_GET:
      $limit = isset( $_GET['limit'] ) ? $_GET['limit'] : self::DEFAULT_LIMIT;
      $users = $this->getRecommendations( $limit );
      $this->addOutput('users', $users);
      break;
    default:
    }

    $this->addOutput( 'result', true );
    $this->output( );
    exit;
  }

  function getRecommendations( $limit ) {
    $limit = (int) $limit;

    if ( $limit <= 0 || $limit > self::MAX_LIMIT ) {
      throw new InvalidArgumentException('Invalid limit.');
    }

    $userRecommendations = new UserRecommendations( );
    $recommendations = $userRecommendations->getRecommendations( $this->viewer->user, $limit );

    $response = array();

    foreach ( $recommendations as $userId ) {
      $user = new Member( $userId );
      $response[] = $user->getProperties();
    }

    return $response;
  }
}
