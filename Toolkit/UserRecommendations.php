<?php

class UserRecommendations {

    function __construct() {
        
    }

    public function getRecommendations(Member $user, $limit) {
        $friends = new Friends();
        $friendList = $friends->get($user);
        $query = sPDO::getInstance( )->prepare( 'SELECT array_intersect( :array1, :array_2);' );
                $query->bindValue(':array1', $user->userId);
        $query->bindValue(':array2', $user->userId);


        $query = sPDO::getInstance()->prepare('SELECT get_user_recommendations(:user_id, :ignore_list, :limit);');
        $query->bindValue(':user_id', $user->userId);
        $query->bindValue(':ignore_list', '{' . implode(',', $friendList) . '}');
        $query->bindValue(':limit', $limit);
        $query->execute();
        //error_log(print_r($query->fetchAll(PDO::FETCH_COLUMN),true));
        return $query->fetchAll(PDO::FETCH_COLUMN);
    }

}
