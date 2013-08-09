<?php

class Landing extends ApplicationBase {

    public function execute() {
        $x = XSLTransformer::getInstance();

        
           //display all the popups
        //    $this->assetsManager->addInitJavaScript("$('.NavigationLink.first').addClass('active');");

            $output = $this->displayEventsPage($x);
        
        $this->display->appendOutput($output);
    }
    
    

    function displayEventsPage($x) {


        $this->assetsManager->addCSS('Landing');
        $this->assetsManager->addCSS('PopUps');

        $this->assetsManager->addJavascript('PopUps');
        $this->assetsManager->addJavascript('Landing');
        $this->assetsManager->addJavascript('jquery.scrollTo');
        $this->assetsManager->addJavascript('jquery.nav');

        $node = $this->dom->createElement('Landing');
        $node->appendChild($this->getLoggedInMemberNode());
        $node->appendChild($this->getFeaturedEvents());

        return $x->transform('Landing', $node);
    }

    function getFeaturedEvents() {
        $query = sPDO::getInstance()->prepare("SELECT * FROM featured_events WHERE starts_at > '" .
                date("Y-m-d H:i:s") . "' ORDER BY priority DESC LIMIT 2");
        $query->execute();
        $node = $this->dom->createElement('images');

        foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $featured) {




            $arry = explode(" ", $featured['markup']);
            $img = $this->dom->createElement('image', $arry[1]);

            $evt = $this->getEvent($featured['featured_event_id']);
            $attendanceManager = $this->getAttendanceManager($evt);

            $img->appendChild($this->getAttendeesNode($attendanceManager));
            $img->appendChild($this->getHostNode($evt->host));

            $img->setAttribute('desc', $featured['description']);
            $img->setAttribute('loc', $featured['location']);
            $img->setAttribute('headline', $featured['headline']);
            $img->setAttribute('startDate', $featured['starts_at']);
            $img->setAttribute('id', $featured['featured_event_id']);
            $img->setAttribute('price', $featured['price']);
            $img->setAttribute('host', $featured['host']);
            $img->setAttribute('spots', $featured['total_spots']);
            $img->setAttribute('spots_purchased', $featured['spots_purchased']);



            $datetime = new DateTime($featured['starts_at']);
            $img->appendChild($this->getDateNode(new DateObject($datetime)));

            $node->appendChild($img);
        }
        return $node;
    }

    protected function generateTimes() {
        $times = array();
        $meridies = array("am", "pm");
        foreach ($meridies as $meridiem) {
            for ($hours = 1; $hours <= 12; $hours++) {
                if ($hours < 10)
                        $hours = '0' . $hours;
                for ($mins = 0; $mins < 60; $mins+=15) {
                    
                    if ($mins < 10)
                        $mins = '0' . $mins;
                    
                    $time = $hours . ":" . $mins . " " . $meridiem;
                    $times[] = $time;
                }
            }
        }
        return $times;
    }

//all same methods as featuredEventviewer.php --> refactor - inheritance
    protected function getDateNode(DateObject $date) {
        return $date->toNode($this->dom);
    }

    protected function getEventNode(FeaturedEvent $event) {
        return $event->toNode($this->dom);
    }

    protected function getHostNode($hostId) {
        $host = $this->dom->createElement('Host');
        $member = new Member($hostId);
        $host->appendChild($member->toNode($this->dom));
        return $host;
    }

    protected function getEvent($id) {
        return new FeaturedEvent($id);
    }

    protected function getAttendeesNode($attendanceManager) {
        return $attendanceManager->toNode($this->dom);
    }

    protected function getAttendanceManager(FeaturedEvent $event) {
        $am = new FeaturedEventAttendanceManager($event);
        return $am;
    }

}