<?php

class UserPhoto extends ApplicationBase {

    function execute() {
        $userId = $this->requestValues[0];

        $size = Photo::SIZE_MEDIUM;

        if (isset($this->requestValues[1])) {
            $size = $this->requestValues[1];
        }

        $user = new Member($userId, Member::TYPE_SIMPLE);
        $this->photo = Photo::getByUser($userId, $user->gender);

        error_log('size' . $size == Photo::SIZE_LARGE);
        if ($this->photo->path == 'facebook') {
            if ($size == Photo::SIZE_LARGE)
                $redirect = 'https://graph.facebook.com/' . $user->fb_id . '/picture?type=large';
            else if ($size == Photo::SIZE_SMALL)
                $redirect = 'https://graph.facebook.com/' . $user->fb_id . '/picture?type=square';
            else {
                $redirect = 'https://graph.facebook.com/' . $user->fb_id . '/picture?type=normal';

            }
        }
        else
            $redirect = $this->photo->paths[$size];
        $this->redirect($redirect);
    }

}
