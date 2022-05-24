<?php

namespace App\Models;

class LatLng
{
    public $latitude;
    public $longitude;

    public function __construct($latitude, $longitude) {
        $this->latitude = (double) $latitude;
        $this->longitude = (double) $longitude;
    }

    public function getLatLng() {
        return "lat:{$this->latitude}, lng:{$this->longitude}";
    }

    public function __toString() {
        return "{$this->latitude}, {$this->longitude}";
    }

    public function __isset($name) {
        return isset($this->$name) && !empty($this->$name);
        return empty($this->latitude) || empty($this->longitude);
    }

    public function __serialize() {
        return ["lat" => $this->latitude, "lng" => $this->longitude];
    }

    public static function unserialize(array $data) {
        return new LatLng($data["lat"], $data["lng"]);
    }

    public function __set_state() {
        return serialize($this);
    }
}