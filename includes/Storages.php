<?php


namespace MediaWiki\Extension\BoilerPlate;


class Storages
{

    private $REQUEST_FILE = __DIR__ . "/../storage/HDjRutMUK3YHREQkZjbVMnDRGwVWyrBt.php";
    private $MESSAGE_FILE = __DIR__ . "/../storage/TNybfFrEZRK97Fj5rSdTv8tcZMCCT46J.php";

    private static $prefix = "<?php exit();?> ";

    /**
     * Storages constructor.
     */
    public function __construct()
    {
        if (!file_exists($this->REQUEST_FILE)) $this->set($this->REQUEST_FILE, "[]");
        if (!file_exists($this->MESSAGE_FILE)) $this->set($this->MESSAGE_FILE, "{}");
    }

    private function get($file) {
        return substr(file_get_contents($file), strlen(self::$prefix));
    }

    private function set($file, $text) {
        $text = self::$prefix . $text;
        file_put_contents($file, $text);
    }


    public function loadRequests() {
        return json_decode($this->get($this->REQUEST_FILE), true);
    }


    public function storeRequests($array) {
        $json = json_encode($array);
        $this->set($this->REQUEST_FILE, $json);
    }

    public function loadSettings() {
        return json_decode($this->get($this->MESSAGE_FILE), true);
    }

    public function storeMessage($array) {
        $json = json_encode($array);
        $this->set($this->MESSAGE_FILE, $json);

    }
}