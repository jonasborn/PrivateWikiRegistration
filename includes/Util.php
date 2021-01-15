<?php

require_once __DIR__ . '/../../../maintenance/Maintenance.php';
require_once __DIR__ . '/../../../LocalSettings.php';

class Util {
static function getPage($self, $name)
{
    $params = new DerivativeRequest(
        $self->getRequest(), // Fallback upon $wgRequest if you can't access context.
        array(
            'action' => 'parse',
            'page' => $name,
            'prop' => 'wikitext'
        )
    );

    try {
        $api = new ApiMain($params, true);
        $api->execute();
        $data = $api->getResult()->getResultData();
        return $data["parse"]["wikitext"];
    } catch (Exception $e) {
        $title = Title::newFromText($name);
        $url = $title->getEditURL();
        return str_replace("{}", "<a href='$url'>$name</a>", $self->msg("template-missing"));
    }
}

}