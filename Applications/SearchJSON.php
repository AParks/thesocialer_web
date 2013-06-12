<?php

class SearchJSON extends JSONApplicationBase
{
  const PARAMETER_QUERY = 'query';
  const ACTION_SEARCH_USER = 'searchuser';
  const ACTION_SEARCH_LOCATION = 'searchlocation';
  
  public function execute( )
  {
    $success = false;

    
    switch ( $this->requestValues[0] )
    {
    	case self::ACTION_SEARCH_USER:
    		$success = $this->searchUsers($_GET['query']);
    		break;
    	case self::ACTION_SEARCH_LOCATION:
    		$success = $this->searchLocations($_GET['query']);
    		break;
    }

    $this->addOutput( 'result', $success );
    $this->output( );
    exit;
  }

  function searchUsers($query)
  {
  	try {
	    $search = new UserSearch();
	    $response = array();
	    foreach ($search->query($query) as $user)
	    {
	    	$response[] = $user->getProperties();
	    }
	    $this->addOutput('results', $response);
	    return true;
  	}
  	catch ( Exception $e ) {
  		$this->addOutput( 'error', $e->getMessage( ) );
  		return false;
  	}
  }
  
  function searchLocations($search)
  {
  	try {
  		$querytext = 'SELECT location_id '
  		.'FROM locations '
  		.'WHERE location_name ~* :query '
  		.'ORDER BY location_name DESC '
  		.'LIMIT 5 ';
  		$query = sPDO::getInstance( )->prepare($querytext);
  		$query->bindValue(':query', $search);
  		$query->execute();
  		$locIds = $query->fetchAll(PDO::FETCH_COLUMN, 0);
  		
  		$locs = array();
  		
  		foreach ( $locIds as $locId )
  		{
  			$locs[] = new Location($locId);
  		}

  		$response = array();
  		foreach ($locs as $location)
  		{
  			$response[] = $location->getProperties();
  		}
  		
  		$this->addOutput('results', $response);
  		return true;
  	}
  	catch ( Exception $e ) {
  		$this->addOutput( 'error', $e->getMessage( ) );
  		return false;
  	}
  }
}
