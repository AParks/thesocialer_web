<?php

class PopUps extends ApplicationBase {

    public function execute() {
        $x = XSLTransformer::getInstance();


        $this->assetsManager->addInitJavaScript("$('.NavigationLink.third').addClass('active');");

        //display all the featured events
        $output = $this->displayEventsPage($x);

        $this->display->appendOutput($output);
    }

    function displayEventsPage($x) {


        $this->assetsManager->addCSS('Explore');
        $this->assetsManager->addCSS('PopUps');
        $this->assetsManager->addJavascript('https://maps.googleapis.com/maps/api/js?sensor=false', true);
        $this->assetsManager->addJavascript('PopUps');


        $node = $this->dom->createElement('Popups');
        $node->appendChild($this->getLoggedInMemberNode());

        $node->appendChild($this->getFeaturedEvents());

        return $x->transform('PopUps', $node);
    }

    function getFeaturedEvents() {
        $query = sPDO::getInstance()->prepare("SELECT * FROM featured_events WHERE starts_at > '" .
                date("Y-m-d H:i:s") . "' ORDER BY priority DESC");
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