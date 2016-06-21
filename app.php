<?php

define("BASEDIR", __DIR__);
ini_set("display_errors", 1);
error_reporting(E_ALL);

require_once(BASEDIR . "/config/config.php");
require_once(BASEDIR . "/vendor/autoload.php");

$app = new \Slim\Slim($config["slim"]);
$app->add(new \Zeuxisoo\Whoops\Provider\Slim\WhoopsMiddleware());
$app->view(new \Slim\Views\Twig());

// Load libraries
foreach(glob(BASEDIR . "/libraries/*.php") as $lib)
    require_once($lib);



// Routes
$app->get("/", function() use ($app, $config) {
    $state = new stdClass();
    $state->mode = "";
    $state->inviteCode = $app->request->get("inviteCode");
    $state->fleetId = $app->request->get("fleetId");
    $base64State = base64_encode(json_encode($state));
    $app->render("index.twig", array("crestURL" => "https://login.eveonline.com/oauth/authorize?response_type=code&redirect_uri=" . $config['sso']['callbackURL'] . "&client_id=" . $config['sso']['clientID'] . "&scope=publicData&state=$base64State"));
});

$app->get("/publish/", function() use ($app, $config) {
    $state = new stdClass();
    $state->mode = "publish";
    $state->fleetId = $app->request->get("fleetId");
    $app->render("index.twig", array("crestURL" => "https://login.eveonline.com/oauth/authorize?response_type=code&redirect_uri=" . $config['sso']['callbackURL'] . "&client_id=" . $config['sso']['clientID'] . "&scope=publicData+fleetRead+fleetWrite+characterLocationRead&state=$base64State"));
});

$app->get("/close/", function() use ($app, $config) {
    $state = new stdClass();
    $state->mode = "close";
    $state->fleetId = $app->request->get("fleetId");
    $app->render("index.twig", array("crestURL" => "https://login.eveonline.com/oauth/authorize?response_type=code&redirect_uri=" . $config['sso']['callbackURL'] . "&client_id=" . $config['sso']['clientID'] . "&scope=publicData+fleetRead+fleetWrite+characterLocationRead&state=$base64State"));
});

$app->get("/auth/", function() use ($app, $config) {s
    $code = $app->request->get("code");
    $base64State = $app->request->get("state");
    $state = json_decode(base64_decode($base64State));

    $tokenURL = "https://login.eveonline.com/oauth/token";
    $base64 = base64_encode($config["sso"]["clientID"] . ":" . $config["sso"]["secretKey"]);

    $data = json_decode(sendData($tokenURL, array(
        "grant_type" => "authorization_code",
        "code" => $code
    ), array("Authorization: Basic {$base64}")));

    $accessToken = $data->access_token;


    // Verify Token
    $verifyURL = "https://login.eveonline.com/oauth/verify";
    $data = json_decode(sendData($verifyURL, array(), array("Authorization: Bearer {$accessToken}")));

    $characterID = $data->CharacterID;
    $characterData = json_decode(json_encode(new SimpleXMLElement(getData("https://api.eveonline.com/eve/CharacterInfo.xml.aspx?characterID={$characterID}"))));
    $corporationID = $characterData->result->corporationID;
    $allianceID = $characterData->result->allianceID;

    if($state->mode == "publish")
    {
        $crestResult = makeCrestRequest("/fleets/$state->fleetId/", $accessToken);
        if($crestResult != false)
        {
            $inviteCode = uniqid();
            insertInviteCode($config["db"]["url"], $config["db"]["user"], $config["db"]["pass"], $config["db"]["dbname"],
                $state->fleetId, $inviteCode, $accessToken);
        }
    
        $app->render("authedManage.twig", array("inviteLink" => $inviteLink, "authString" => $authString));
    }
    else if($state->mode == "close")
    {
        $crestResult = makeCrestRequest("/fleets/$state->fleetId/", $accessToken);
        if($crestResult != false)
        {
            deleteInviteCode($config["db"]["url"], $config["db"]["user"], $config["db"]["pass"], $config["db"]["dbname"], 
                $state->fleetId);
        }
    
        $app->render("authedManage.twig", array("inviteLink" => $inviteLink, "authString" => $authString));
    }
    else
    {
        
        $app->render("authed.twig", array("inviteLink" => $inviteLink, "authString" => $authString));
    }

    // Generate an auth string

});

$app->run();

/**
 * Var_dumps and dies, quicker than var_dump($input); die();
 *
 * @param $input
 */
function dd($input)
{
    var_dump($input);
    die();
}
