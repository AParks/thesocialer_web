<?php

class FeaturedEventViewer extends ApplicationBase {

    public function execute() {

        $x = XSLTransformer::getInstance();

        $event = $this->getEvent();


        //$date = LocationViewer::getDate( );
        $attendanceManager = $this->getAttendanceManager($event);

        $node = $this->dom->createElement('FeaturedEventViewer');
        $node->appendChild($this->getLoggedInMemberNode());
        $node->appendChild($this->getEventNode($event));
        $node->appendChild($this->getDateNode(new DateObject(new DateTime($event->starts_at))));
        $node->appendChild($this->getAttendeesNode($attendanceManager));

        $this->assetsManager->addInitJavaScript(' var evt = ' . json_encode($event->getPublicProperties()) . ';');
        //  $this->assetsManager->addInitJavaScript('var userStatus = "' . $attendanceManager->getUserStatus($this->viewer->userId) . '";');


        $this->assetsManager->addJavaScript('jquery.corner');
        $this->assetsManager->addJavaScript('jquery.linkify-1.0-min');
        $this->assetsManager->addJavaScript('FeaturedEventViewer');
        $this->assetsManager->addJavaScript('AttendanceManager');
        $this->assetsManager->addJavascript('https://maps.googleapis.com/maps/api/js?sensor=false', true);
        $this->assetsManager->addInitJavaScript("$('.NavigationLink.third').addClass('active');");
        $this->assetsManager->addCSS('FeaturedEventViewer');

        $output = $x->transform('FeaturedEventViewer', $node);
        $this->display->appendOutput($output);
      
        
    }

    protected function getDateNode(DateObject $date) {
        return $date->toNode($this->dom);
    }

    protected function getEventNode(FeaturedEvent $event) {
        return $event->toNode($this->dom);
    }

    protected function getEvent() {
        return new FeaturedEvent($this->requestValues[0]);
    }

    protected function getAttendeesNode($attendanceManager) {
        return $attendanceManager->toNode($this->dom);
    }

    protected function getAttendanceManager(FeaturedEvent $event) {
        $am = new FeaturedEventAttendanceManager($event);
        return $am;
    }

}
