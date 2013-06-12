<?php

class FeaturedEvent extends ATransformableObject {

    protected $featured_event_id;
    protected $description;
    protected $starts_at;
    protected $ends_at;
    protected $location;
    protected $markup;
    protected $price;
    protected $headline;
    protected $is_private;
    protected $sub_headline;
    protected $host;
    protected $priority;
    protected $publicProperties = array('featured_event_id', 'description', 'starts_at', 'ends_at', 'location', 'markup','price',  'headline', 'is_private', 'sub_headline', 'host', 'priority');

    public function __construct($featuredEventId = null) {
        $this->_load($featuredEventId);
    }

    private function _getRandom() {
        $query = sPDO::getInstance()->prepare('SELECT featured_event_id, description, starts_at, ends_at, location, markup FROM featured_events( CURRENT_TIMESTAMP )');
        $query->execute();
        $featuredEvents = $query->fetchAll(PDO::FETCH_OBJ);
        //var_export($featuredEvents[0]);
        if ($featuredEvents) {
            return $featuredEvents[array_rand($featuredEvents)];
        }
        return null;
    }

    private function _getFirst() {
        $querytext = 'SELECT featured_event_id, description, starts_at, ends_at, location, markup, price,  headline '
                . 'FROM featured_events( CURRENT_TIMESTAMP ) '
                . 'ORDER BY starts_at ASC';
        $query = sPDO::getInstance()->prepare($querytext);
        $query->execute();
        $featuredEvents = $query->fetchAll(PDO::FETCH_OBJ);
        if ($featuredEvents) {
            return $featuredEvents[0];
        }
        return null;
    }

    private function _get($featuredEventId) {
        $query = sPDO::getInstance()->prepare('SELECT * FROM featured_events( :event_id::INT )');
        $query->bindValue(':event_id', $featuredEventId, PDO::PARAM_INT);
        $query->execute();
        return $query->fetch(PDO::FETCH_OBJ);
    }

    private function _load($featuredEventId = null) {
        if ($featuredEventId) {
            $event = $this->_get($featuredEventId);
        } else {
            $event = $this->_getFirst();
        }

        if (!$event) {
            throw new Exception('Event not found');
        }

        $this->featured_event_id = $event->featured_event_id;
        $this->description = $event->description;
        $this->starts_at = $event->starts_at;
        $this->ends_at = $event->ends_at;
        $this->location = $event->location;
        $this->markup = $event->markup;
        $this->price = $event->price;
        $this->headline = $event->headline;
        $this->sub_headline = $event->sub_headline;
        $this->host = $event->host;
        $this->priority = $event->priority;


    }

    public function getPublicProperties() {
        $properties = array();

        foreach ($this->publicProperties as $property) {
            if (is_scalar($this->$property) === true) {
                $properties[$property] = $this->$property;
            }
        }

        return $properties;
    }

}
