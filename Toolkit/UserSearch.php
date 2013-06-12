<?php

class UserSearch
{
  function query($search)
  {
    $query = sPDO::getInstance( )->prepare('SELECT user_id FROM search(:query)');
    $query->bindValue(':query', $search);
    $query->execute();
    $userIds = $query->fetchAll(PDO::FETCH_COLUMN, 0);

    $users = array();

    foreach ( $userIds as $userId )
    {
      $users[] = new Member($userId);
    }

   return $users;
  }
}
