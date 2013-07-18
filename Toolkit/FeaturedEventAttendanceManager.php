<?php

class FeaturedEventAttendanceManager extends ATransformableObject {

    const ATTENDANCE_YES = 'yes';
    const ATTENDANCE_NO = 'no';
    const ATTENDANCE_MAYBE = 'maybe';
    const ATTENDANCE_NOT_SET = '';
    const TYPE_SIMPLE = 'simple';
    const TYPE_COMPLEX = 'complex';
    const TYPE_COUNT = 'count';

    protected $validStatuses = array(self::ATTENDANCE_YES,
        self::ATTENDANCE_NO,
        self::ATTENDANCE_MAYBE);
 //   protected $attendanceStatuses = array(self::ATTENDANCE_YES => array(),
 //       self::ATTENDANCE_MAYBE => array(),
 //       self::ATTENDANCE_NO => array());
    
      protected $attendanceStatuses = array();
    protected $featuredEvent;
    protected $publicProperties = array('attendanceStatuses');

    public function __construct($featuredEvent) {
        $this->featuredEvent = $featuredEvent;
        $this->_load( );
    }

    protected function _load() {
        $pdo = sPDO::getInstance();
        $query = $pdo->prepare('SELECT DISTINCT user_id FROM get_featured_event_paid_attendance_list( :event_id )');
        $query->bindParam(':event_id', $this->featuredEvent->featured_event_id);

        if (!$query->execute()) {
            throw new Exception('Query.' . print_r($query->errorInfo(), true));
        }

        foreach ($query->fetchAll(PDO::FETCH_OBJ) as $row) {
            $this->attendanceStatuses[] = new Member($row->user_id);
        }
    }

//  protected function _load( ) {
//    $pdo = sPDO::getInstance( );
//    $query = $pdo->prepare( 'SELECT user_id, attendance_status FROM get_featured_event_attendance_statuses( :event_id )' );
//    $query->bindParam ( ':event_id', $this->featuredEvent->featuredEventId );
//    
//    if ( ! $query->execute( ) ) {
//      throw new Exception( 'Query.' . print_r( $query->errorInfo( ), true ) );
//    } 
//
//    foreach ( $query->fetchAll( PDO::FETCH_OBJ ) as $row ) {
//      $this->attendanceStatuses[$row->attendance_status][] = new Member( $row->user_id );
//    }
//  }
//  public function getAttendanceCount( $type ) {
//    if ( in_array( $type, $this->validStatuses ) === false ) {
//      throw new InvalidArgumentException( 'Invalid status' );
//    }
//    return count( $this->attendanceStatuses[$type] );
//  }
//
//  public function getAttendanceStatuses( $type = self::TYPE_SIMPLE ) {
//    switch ( $type ) {
//    case self::TYPE_COMPLEX:
//      return $this->attendanceStatuses;
//      break;
//    case self::TYPE_COUNT:
//      $return = array( );
//      foreach ( $this->attendanceStatuses as $status => $users ) {
//        $return[$status] = count( $users );
//      }
//      return $return;
//      break;
//    default:
//      $return = array( );
//      foreach ($this->attendanceStatuses as $status => $users) {
//        $return[$status] = array();
//        foreach ( $users as $user) {
//          $return[$status][] = $user->userId;
//        }
//      }
//      return $return;
//      break;
//    }
//  }
//
//  public function setStatus( $userId, $status ) {
//    if ( in_array( $status, $this->validStatuses ) === false ) {
//      throw new InvalidArgumentException( 'Invalid status' );
//    }
//
//    $pdo = sPDO::getInstance( );
//    $query = $pdo->prepare( 'SELECT set_featured_event_attendance_status( :user_id, :event_id, :attendance_status )' );
//    $query->bindValue( ':user_id', $userId );
//    $query->bindValue( ':event_id', $this->featuredEvent->featuredEventId );
//    $query->bindValue( ':attendance_status', $status );
//    $result = $query->execute( );
//
//    if ( $result ) {
//      foreach ( $this->attendanceStatuses as $type => $users ) {
//        foreach ( $users as $key => $user ) {
//          if ( $user->userId == $userId ) {
//            unset( $this->attendanceStatuses[$type][$key] );;
//          }
//        }
//      }
//
//      $this->attendanceStatuses[$status][] = $userId;
//    }
//
//    return $result;
//  }
//
//  public function getUserStatus( $userId ) {
//    $pdo = sPDO::getInstance( );
//    $query = $pdo->prepare( 'SELECT get_user_featured_event_attendance_status( :user_id, :event_id)' );
//    $query->bindValue( ':user_id', $userId );
//    $query->bindValue( ':event_id', $this->featuredEvent->featuredEventId);
//    $query->execute( );
//    $status = $query->fetchColumn( 0 );
//
//    if ( ! $status ) {
//      $status = self::ATTENDANCE_NOT_SET;
//    }
//
//    return $status;
//  }
}
