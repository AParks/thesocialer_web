<?php

class LocationJSON extends JSONApplicationBase {
  const ACTION_SET_ATTENDANCE = 'setAttendance';
  const ACTION_LOCATIONS_FOR_DATE = 'forDate';
  const ACTION_POPULAR = 'popular';
  const ACTION_PAST_EVENTS = 'pastEvents';
  const ACTION_LIKED_LOCATIONS = 'likedLocations';

  const PARAMETER_LIMIT = 'limit';
  const PARAMETER_DATE = 'date';
  const PARAMETER_OFFSET = 'offset';

  const PARAMETER_LOCATION = 'location';
  const PARAMETER_STATUS = 'status';

  public function execute( ) {

    $success = false;
    $shouldoutput = true;
    try {

      date_default_timezone_set("America/New_York");
      switch ( $this->requestValues[0] ) {
      case self::ACTION_SET_ATTENDANCE:
        if ( isset( $_GET['eventId'] ) ) {
          $success = $this->setEventStatus( $this->viewer->userId, $_GET['eventId'], $_GET[self::PARAMETER_STATUS]);
        }
        else {
          $success = $this->setStatus( $this->viewer->userId, $_GET[self::PARAMETER_LOCATION], $_GET[self::PARAMETER_STATUS], $_GET[self::PARAMETER_DATE] );
        }
        break;
      case self::ACTION_LOCATIONS_FOR_DATE:
        $success = $this->getLocationsForDate( new DateTime( $_GET[self::PARAMETER_DATE] ), $_GET[self::PARAMETER_LIMIT] );
        break;
      case self::ACTION_POPULAR:
        $success = $this->getPopularLocations(new DateTime(), $_GET[self::PARAMETER_LIMIT],
               $_GET[self::PARAMETER_OFFSET] ? $_GET[self::PARAMETER_OFFSET] : 0);
        break;
      case self::ACTION_PAST_EVENTS:
        $success = $this->getPastAttendedEvents( new Member( $_GET['userId'] ), $_GET['limit'], $_GET['offset'] );
        break;
      case self::ACTION_LIKED_LOCATIONS:
      	$success = $this->getLikedLocations( $_GET['userId'] );
      	break;
      default:
	$shouldoutput = false;
	break;
      }
    }
    catch ( Exception $e ) {
      $this->addOutput( 'error', $e->getMessage( ) );
    }
    $this->addOutput( 'result', $success );
    if( $shouldoutput ) {
      $this->output( );
    }
    exit;
  }

  public function getPopularLocations(DateTime $date, $limit, $offset) {
    $limit = (int) $limit;
    if ( $limit <= 0 ) {
      throw new Exception();
    }

    $date->setTime(0, 0, 0);

    $dateEnd = clone $date;
    // modify this to change the number of days that the home results will look forward
    $dateEnd->modify('+0 days');
    $locationManager = new LocationManager();
    $locationManager->setLimit($limit);
    $locationManager->setOffset($offset);
    $locationManager->setSort(LocationManager::SORT_BY_RELEVANT);
    $locationManager->setSortOption(LocationManager::SORT_OPTION_DATE, $date);
    $locationManager->setSortOption(LocationManager::SORT_OPTION_DATE_END, $dateEnd);
    $locations = $locationManager->getLocations();
    
    foreach ( $locations as $location ) {
      $locationInfo = $location->location->getPublicProperties();
      $locationInfo['date'] = $location->date->format('U');
      $locationInfo['likedByUser'] = $this->viewer->user->likesLocation($location->location->id);
      $locationInfo['tags'] = $location->location->tags;
      $response[] = $locationInfo;
    }

    $this->addOutput( 'locations', $response );
    $this->addOutput( 'count', $locationManager->getCount());
    return true;
  }

  protected function setStatus( $userId, $locationId, $status, $date ) {
                                  error_log("hello setting status");

    $am = new AttendanceManager( new Location( $locationId ), new DateTime( $date ) );
    $am->setStatus( $userId, $status );
    return $am->getAttendanceStatuses( AttendanceManager::TYPE_COUNT );
  }

  protected function setEventStatus( $userId, $eventId, $status ) {
    $am = new FeaturedEventAttendanceManager( new FeaturedEvent( $eventId ) );
    $am->setStatus( $userId, $status );
  }

  protected function getLocationsForDate( DateTime $date, $limit ) {
    $locationManager = new LocationManager( );
    $locationManager->setLimit( $limit );
    $locationManager->setSort( LocationManager::SORT_BY_ATTENDING_COUNT );
    $locationManager->setSortOption( LocationManager::SORT_OPTION_DATE, $date );
    $locations = $locationManager->getLocations( );

    $response = array( );

    foreach ( $locations as $location ) {
      $attendanceManager = new AttendanceManager( $location, $date );
      $attendees = $attendanceManager->getAttendanceStatuses();
      $attendees = $attendees[AttendanceManager::ATTENDANCE_YES];
      $locationInfo = $location->getPublicProperties();
      $locationInfo['attendees'] = $attendees;
      $locationInfo['userStatus'] = $attendanceManager->getUserStatus( $this->viewer->user->userId );
      $locationInfo['attendanceCounts'][AttendanceManager::ATTENDANCE_YES] = $attendanceManager->getAttendanceCount(AttendanceManager::ATTENDANCE_YES);
      $locationInfo['attendanceCounts'][AttendanceManager::ATTENDANCE_MAYBE] = $attendanceManager->getAttendanceCount(AttendanceManager::ATTENDANCE_MAYBE);
      $response[] = $locationInfo;
    }

    $this->addOutput( 'locations', $response );
  }

  protected function getPastAttendedEvents( Member $user, $limit, $offset )
  {
    $response = array( );

    $userAttendanceManager = new UserAttendanceManager( );
    $pastAttendedEvents = $userAttendanceManager->getPastAttendedEvents( $user, $limit, $offset );

    foreach ( $pastAttendedEvents as $event )
    {
      $eventInfo = $event->location->getPublicProperties( );
      $eventInfo['date'] = $event->date;
      $eventInfo['attendanceStatus'] = $event->attendanceStatus;
      $response[] = $eventInfo;
    }

    $this->addOutput( 'pastEvents', $response );
    return true;
  }
  
  protected function getLikedLocations( $userId )
  {
  	$response = array( );
  
  	$query = sPDO::getInstance()->prepare('SELECT l.location_id, l.location_name, l.location_image FROM locations l, location_likes ll WHERE ll.user_id = :user_id AND l.location_id = ll.location_id');
  	$query->bindValue(':user_id', $userId);
  	$query->execute();
  	
  	
  	foreach ( $query->fetchAll(PDO::FETCH_ASSOC) as $loc )
  	{
  		$locInfo = array();
  		$locInfo['id'] = $loc['location_id'];
  		$locInfo['name'] = $loc['location_name'];
  		$locInfo['image'] = $loc['location_image'];
  		$response[] = $locInfo;
  	}
  
  	$this->addOutput( 'likedLocations', $response );
  	return true;
  }
}
