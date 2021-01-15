<?php /** @noinspection ALL */


require_once __DIR__ . "/Storages.php";
require_once __DIR__ . "/Util.php";

require_once __DIR__ . '/../../../maintenance/Maintenance.php';
require_once __DIR__ . '/../../../LocalSettings.php';

class JoinPage extends SpecialPage
{

    function __construct()
    {
        parent::__construct('Join');
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
        $requests = $storage->loadRequests();


        $request = $this->getRequest();
        $output = $this->getOutput();
        $this->setHeaders();

        # Get request data from, e.g.
        $username = $request->getText('username');
        $token = $request->getText("token");
        $password = $request->getText("password");

        $output->setPageTitle($this->msg("join.page-title"));

        if (empty($username) && empty($password)) {
            $output->redirect(SpecialPage::getTitleFor('Registration')->getLocalURL(''));
        }

        if (!empty($username) && empty($password)) {
            $output->setPageTitle("Beitreten");
            $output->addHTML(Util::getPage($this, "Template:Join"));
        }

        if (!empty($username) && !empty($password)) {
            $index = 0;
            foreach ($requests as $entry) {
                if ($entry["username"] == $username) break;
                $index++;
            }

            $entry = $requests[$index];
            $storageUsername = $entry["username"];
            $storageToken = $entry["token"];

            $user = User::newFromName($username);

            if (!$user || !$user->getId()) {
                $output->addHTML("Unable to find user " . $username);
            } else {
                $status = $user->changeAuthenticationData([
                    'username' => $user->getName(),
                    'password' => $password,
                    'retype' => $password,
                ]);

                $output->addHTML("Sucessfully updated " . $username);
                unset($requests[$index]);
                $storage->storeRequests($requests);
                $output->redirect(SpecialPage::getTitleFor('Login')->getLocalURL(''));

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


}