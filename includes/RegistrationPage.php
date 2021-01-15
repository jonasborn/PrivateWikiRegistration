<?php /** @noinspection ALL */

include_once __DIR__ . "/Storages.php";
include_once __DIR__ . "/Util.php";

require_once __DIR__ . '/../../../maintenance/Maintenance.php';
require_once __DIR__ . '/../../../LocalSettings.php';


class RegistrationPage extends SpecialPage
{

    function __construct()
    {
        parent::__construct('Registration');

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
        $storage = new \MediaWiki\Extension\BoilerPlate\Storages();
        $settings = $storage->loadSettings();


        $request = $this->getRequest();
        $output = $this->getOutput();
        $this->setHeaders();

        $username = $request->getText('username');


        if (empty($username)) {
            $output->setPageTitle($this->msg("registration.page-title")->parse());
            $output->addHTML(Util::getPage($this, $this->msg("registration.template")->parse()));
        } else {

            $token = $this->request(array(
                "action" => "query",
                "meta" => "tokens",
                "type" => "createaccount"
            ))["query"]["tokens"]["createaccounttoken"];

            $password = $this->generateRandomString(20); //Never used again
            $result = $this->request(array("action" => "createaccount",
                "createtoken" => $token,
                "username" => $username,
                "password" => $password,
                "retype" => $password,
                "createreturnurl" => $wgServer));


            if ($result["createaccount"]["status"] == "PASS") {
                $output->setPageTitle($this->msg("summary.page-title")->parse());
                $output->addHTML(Util::getPage($this, $this->msg("summary.template")->parse()));

                $requests = $storage->loadRequests();
                array_push($requests, ["username" => $username, "confirmed" => false]);

                $storage->storeRequests($requests);

            } else {
                $output->setPageTitle($this->msg("failure.page-title")->parse());
                $output->addHTML($result["createaccount"]["message"]);
                $output->addHTML("<br>");
                $output->addHTML("<a href=''>" . $this->msg("failure.retry")->parse() . "</a>");
            }

        }

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


}