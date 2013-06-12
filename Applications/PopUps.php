<?php

class PopUps extends ApplicationBase {

    public function execute() {
        $x = XSLTransformer::getInstance();

        $featured_event = $this->getFeaturedEvent();
        $this->assetsManager->addInitJavaScript("$('.NavigationLink.third').addClass('active');");

        if ($featured_event == null) //display all the featured events
            $output = $this->displayEventsPage($x);
        
        $this->display->appendOutput($output);
    }

    function displayEventsPage($x) {


        $this->assetsManager->addCSS('Explore');
        $this->assetsManager->addCSS('PopUps');
        $this->assetsManager->addJavascript('https://maps.googleapis.com/maps/api/js?sensor=false', true);


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
            $img->setAttribute('desc', $featured['description']);
            $img->setAttribute('loc', $featured['location']);
            $img->setAttribute('headline', $featured['headline']);
            $img->setAttribute('startDate', $featured['starts_at']);
            $img->setAttribute('id', $featured['featured_event_id']);
            $img->setAttribute('price', $featured['price']);


            $datetime = new DateTime($featured['starts_at']);
            $img->appendChild($this->getDateNode(new DateObject($datetime)));





            $node->appendChild($img);
        }
        return $node;
    }

    protected function getDateNode(DateObject $date) {
        return $date->toNode($this->dom);
    }

    protected function getFeaturedEventNode(FeaturedEvent $featured_event) {
        return $featured_event->toNode($this->dom);
    }

    protected function getFeaturedEvent() {
        if ($this->requestValues[0])
            return new FeaturedEvent($this->requestValues[0]);
        else
            return null;
    }

}