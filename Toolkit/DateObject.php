<?php

class DateObject extends ATransformableObject {

    protected $dateTime;
    protected $publicProperties = array('shortDay', 'longDay', 'shortMonth', 'longMonth', 'date', 'rawdate', 'year', 'time');

    public function __construct(DateTime $date) {
        $this->dateTime = $date;
    }

    public function __get($key) {
        $response = null;

        switch ($key) {
            case 'shortDay':
                $response = $this->dateTime->format('D');
                break;
            case 'longDay':
                $response = $this->dateTime->format('l');
                break;
            case 'shortMonth':
                $response = $this->dateTime->format('M');
                break;
            case 'longMonth':
                $response = $this->dateTime->format('F');
                break;
            case 'date':
                $response = $this->dateTime->format('j');
                break;
            case 'rawdate':
                $response = $this->dateTime->format('Y-m-d');
                break;
            case 'year':
                $response = $this->dateTime->format('Y');
                break;
            case 'time':
                $miltary = $this->dateTime->format('H:i');
                $response = date('g:i a', strtotime($miltary)); //convert to standard time
                break;
        }

        if ($response !== null) {
            return $response;
        }

        throw new Exception(sprintf('Unknown property: %s', $key));
    }

}
