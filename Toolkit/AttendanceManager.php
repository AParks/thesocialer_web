<?php

class AttendanceManager extends ATransformableObject
{
  const ATTENDANCE_YES = 'yes';
  const ATTENDANCE_NO = 'no';
  const ATTENDANCE_MAYBE = 'maybe';
  const ATTENDANCE_NOT_SET = '';

  const TYPE_SIMPLE = 'simple';
  const TYPE_COMPLEX = 'complex';
  const TYPE_COUNT = 'count';

  protected $validStatuses = array( self::ATTENDANCE_YES,
                                    self::ATTENDANCE_NO,
                                    self::ATTENDANCE_MAYBE );

  protected $location;
  protected $attendanceStatuses = array(self::ATTENDANCE_YES => array(),
                                        self::ATTENDANCE_MAYBE => array(),
                                        self::ATTENDANCE_NO => array());
  protected $date;

  protected $publicProperties = array( 'location', 'attendanceStatuses', 'date' );

  public function __construct( Location $location, DateTime $date )
  {
    $this->location = $location;
    $this->date = $date;
    $this->_load( );
  }

  protected function _load( )
  {

    $pdo = sPDO::getInstance( );
    $id = $this->location->id;
    $query = $pdo->prepare( 'SELECT user_id, attendance_status FROM get_attendance_statuses_for_date( :location_id, :date )' );
    $query->bindParam ( ':location_id', $id );
    $query->bindParam ( ':date', $this->date->format( 'Y-m-d' ) );
    
    if ( ! $query->execute( ) )
    {
      throw new Exception( 'Query.' . print_r( $query->errorInfo( ), true ) );
    } 

    foreach ( $query->fetchAll( PDO::FETCH_OBJ ) as $row )
    {
      $this->attendanceStatuses[$row->attendance_status][] = new Member( $row->user_id );
    }
  }

  public function getAttendanceCount( $type )
  {
    if ( in_array( $type, $this->validStatuses ) === false )
    {
      throw new InvalidArgumentException( 'Invalid status' );
    }
    return count( $this->attendanceStatuses[$type] );
  }

  public function getAttendanceStatuses( $type = self::TYPE_SIMPLE )
  {
    switch ( $type )
    {
    case self::TYPE_COMPLEX:
      return $this->attendanceStatuses;
      break;
    case self::TYPE_COUNT:
      $return = array( );
      foreach ( $this->attendanceStatuses as $status => $users )
      {
        $return[$status] = count( $users );
      }
      return $return;
      break;
    default:
      $return = array( );
      foreach ($this->attendanceStatuses as $status => $users)
      {
        $return[$status] = array();
        foreach ( $users as $user)
        {
          $return[$status][] = $user->userId;
        }
      }
      return $return;
      break;
    }
  }

  public function setStatus( $userId, $status )
  {
            error_log("user status is " . $status);
    if ( in_array( $status, $this->validStatuses ) === false )
    {
      throw new InvalidArgumentException( 'Invalid status' );
    }
            error_log("after user status is" . $status);

    $pdo = sPDO::getInstance( );
    $query = $pdo->prepare( 'SELECT set_attendance_status( :user_id, :location_id, :attendance_status, :date )' );
    $query->bindValue( ':user_id', $userId );
    $query->bindValue( ':location_id', $this->location->id );
    $query->bindValue( ':attendance_status', $status );
    $query->bindValue( ':date', $this->date->format( 'Y-m-d' ) );
    $result = $query->execute( );
      error_log("query result" . $result);

    if ( $result )
    {
      foreach ( $this->attendanceStatuses as $type => $users )
      {
        foreach ( $users as $key => $user )
        {
          if ( $user->userId == $userId )
          {
            unset( $this->attendanceStatuses[$type][$key] );;
          }
        }
      }

      $this->attendanceStatuses[$status][] = $userId;
    }
    //var_dump($this->attendanceStatuses);

    return $result;
  }

  public function getUserStatus( $userId )
  {
    $pdo = sPDO::getInstance( );
    $query = $pdo->prepare( 'SELECT get_user_attendance_status( :user_id, :location_id, :date )' );
    $query->bindValue( ':user_id', $userId );
    $query->bindValue( ':location_id', $this->location->id );
    $query->bindValue( ':date', $this->date->format( 'Y-m-d' ) );
    $query->execute( );
    $status = $query->fetchColumn( 0 );

    if ( ! $status )
    {
      $status = self::ATTENDANCE_NOT_SET;
    }

    return $status;
  }
}
