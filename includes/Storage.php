<?php

$REQUESTS_FILE = __DIR__ . "/../storage/requests.json";
$MESSAGE_FILE = __DIR__ . "/../storage/settings.json";
$a = 1; // globaler Geltungsbereich
function loadRequests() {
    global $REQUESTS_FILE;
    return json_decode(file_get_contents($REQUESTS_FILE), true);
}


function storeRequests($array) {
    global $REQUESTS_FILE;
    $json = json_encode($array);
    file_put_contents($REQUESTS_FILE, $json);
}

function loadSettings() {
    global $MESSAGE_FILE;
    var_dump($MESSAGE_FILE);
    return json_decode(file_get_contents($MESSAGE_FILE), true);
}

function storeMessage($array) {
    global $MESSAGE_FILE;
    $json = json_encode($array);
    file_put_contents($MESSAGE_FILE, $json);

}