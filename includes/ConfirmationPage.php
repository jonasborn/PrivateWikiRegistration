<?php /** @noinspection ALL */

include_once __DIR__ . "/Storages.php";

require_once __DIR__ . '/../../../maintenance/Maintenance.php';
require_once __DIR__ . '/../../../LocalSettings.php';

class ConfirmationPage extends SpecialPage
{

    function __construct()
    {
        parent::__construct('Confirm', "userrights");
    }

    /**
     * Group this special page under the correct header in Special:SpecialPages.
     *
     * @return string
     */
    function getGroupName()
    {
        return 'registration';
    }

    function execute($par)
    {

        global $wgServer;
        $this->checkPermissions();

        $storage = new \MediaWiki\Extension\BoilerPlate\Storages();
        $settings = $storage->loadSettings();

        $request = $this->getRequest();
        $output = $this->getOutput();
        $this->setHeaders();

        # Get request data from, e.g.
        $username = $request->getText('username');

        $requests = $storage->loadRequests();

        if (empty($username)) {
            if (empty($requests)) {
                $output->addHTML("<strong>" . $this->msg("overview.empty")->parse() . "</strong>");
            }
            foreach ($requests as $entry) {
                $username = $entry["username"];
                $output->addHTML("<h1>$username</h1>");
                $output->addHTML("<strong>" . $this->msg("overview.username")->parse() . ":</strong> $username<br>");
                $output->addHTML("<a href='?action=confirm&username=$username'>" . $this->msg("overview.confirm")->parse() . "</a> - ");
                $output->addHTML("<a href='?action=delete&username=$username'>" . $this->msg("overview.delete")->parse() . "</a>");
            }

        } else {
            $action = $request->getText("action");
            $index = 0;
            foreach ($requests as $entry) {
                if ($entry["username"] == $username) break;
                $index++;
            }
            switch ($action) {
                case "delete":
                    unset($requests[$index]);
                    $storage->storeRequests($requests);
                    $output->redirect(SpecialPage::getTitleFor('Confirm')->getLocalURL(''));
                    break;
                case "confirm":
                    $entry = $requests[$index];
                    $username = $entry["username"];
                    $token = $this->generateRandomString(30);

                    $requests[$index]["confirmed"] = true;
                    $requests[$index]["token"] = $token;
                    $url = Title::newFromText("Special:Join")->getLocalURL();
                    $output->addHTML("User $username was confirmed, please invite with this link:<br>");
                    $output->addHTML("<strong>$wgServer$url?username=$username&token=$token</strong>");
                    $storage->storeRequests($requests);
            }

        }

    }

    public function generateRandomString($length = 20)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    function request($array)
    {
        $params = new DerivativeRequest(
            $this->getRequest(), // Fallback upon $wgRequest if you can't access context.
            $array
        );
        $api = new ApiMain($params, true);
        $api->execute();
        $data = $api->getResult()->getResultData();
        return $data;
    }

    function getPage($name)
    {
        $params = new DerivativeRequest(
            $this->getRequest(), // Fallback upon $wgRequest if you can't access context.
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
            $title = SpecialPage::getTitleFor($name);
            $url = $title->getFullURL([], false, PROTO_HTTPS);
            return str_replace("{}", "<a href='$url'>$name</a>", $this->msg("summary.template-missing"));
        }
    }


}