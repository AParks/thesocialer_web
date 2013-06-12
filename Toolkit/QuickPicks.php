<?php

class QuickPicks
{
  function __construct( )
  {

  }

  public function userQuickPicks($userId)
  {
    $return = array();
    $quickPicks = $this->_fetchUserQuickPicks($userId);
    foreach ( $quickPicks as $quickPick )
    {
      if ( isset( $return[$quickPick->quick_pick_topic_id] ) === false )
      {
        $topic = new stdClass;
        $topic->description = $quickPick->quick_pick_topic_description;
        $topic->topic_id = $quickPick->quick_pick_topic_id;
        $topic->values = array( );
        $return[$quickPick->quick_pick_topic_id] = $topic; 
      }

      $value = new stdClass;
      $value->quick_pick_id = $quickPick->quick_pick_id;
      $value->description = $quickPick->quick_pick_description;
      $value->set_at = $quickPick->set_at;
      $return[$quickPick->quick_pick_topic_id]->values[] = $value; 
    }

    return $return;
  }

  private function _fetchUserQuickPicks( $userId )
  {
    $pdo = sPDO::getInstance();
    $query = $pdo->prepare('SELECT quick_pick_id, set_at, quick_pick_description, quick_pick_topic_id, quick_pick_topic_description FROM user_quick_picks(:user_id)');
    $query->bindValue(':user_id', $userId);
    $query->execute();
    return $query->fetchAll(PDO::FETCH_OBJ);
  }
}
