<?php


namespace MediaWiki\Extension\BoilerPlate;


class Storages
{

    private $REQUEST_FILE = __DIR__ . "/../storage/requests.json";
    private $MESSAGE_FILE = __DIR__ . "/../storage/settings.json";

    /**
     * Storages constructor.
     */
    public function __construct()
    {
        if (!file_exists($this->REQUEST_FILE)) file_put_contents($this->REQUEST_FILE, "[]");
        if (!file_exists($this->MESSAGE_FILE)) file_put_contents($this->MESSAGE_FILE, "{}");
    }


    public function loadRequests() {
        return json_decode(file_get_contents($this->REQUEST_FILE), true);
    }


    public function storeRequests($array) {
        $json = json_encode($array);
        file_put_contents($this->REQUEST_FILE, $json);
    }

    public function loadSettings() {
        return json_decode(file_get_contents($this->MESSAGE_FILE), true);
    }

    public function storeMessage($array) {
        $json = json_encode($array);
        file_put_contents($this->MESSAGE_FILE, $json);

    }
}