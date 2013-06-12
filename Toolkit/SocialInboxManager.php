<?php
//ini_set('display_errors', 1);

class SocialInboxManager
{
  function get( Member $user, $dom )
  {
    $node = $dom->createElement('SocialInbox');

    $friendRequestNode = $dom->createElement('FriendRequests');
    $friendRequests = new FriendRequests( );
    foreach ( $friendRequests->getRequests( $user ) as $friendRequest )
    {
      $friendRequestNode->appendChild( $friendRequest->toNode( $dom ) );
    }
    $node->appendChild($friendRequestNode);
    
    $locationShareNode = $dom->createElement('LocationShares');
    $locationShares = new LocationShares( );
    foreach ( $locationShares->getShares( $user ) as $locationShare )
    {
    	$locationShareNode->appendChild( $locationShare->toNode( $dom ) );
    }
    $node->appendChild($locationShareNode);

    return $node;
  }
}
