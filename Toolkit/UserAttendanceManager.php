<?php

class UserAttendanceManager
{
  function __construct( )
  {
  }

  public function getPastAttendedEvents( Member $user, $limit, $offset )
  {
    $limit = (int) $limit;
    $offset = (int) $offset;

    $query = sPDO::getInstance( )->prepare( 'SELECT location_id, EXTRACT(\'EPOCH\' FROM attendance_date) AS attendance_date, attendance_status FROM get_past_user_attendance(:user_id, :limit, :offset)' );
    $query->bindValue( ':user_id', $user->userId );
    $query->bindValue( ':limit', $limit );
    $query->bindValue( ':offset', $offset );
    $query->execute( );
    $records = $query->fetchAll( PDO::FETCH_OBJ );

    $return = array( );

    foreach ( $records as $record )
    {
      $element = new stdclass;
      $element->location = new Location( $record->location_id );
      $element->date = $record->attendance_date;
      $element->attendanceStatus = $record->attendance_status;
      $return[] = $element;
    }

    return $return;
  }
}
