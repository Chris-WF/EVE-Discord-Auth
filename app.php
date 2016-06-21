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
    $inviteCode = "ic_" . $app->request->get("inviteCode");
    $app->render("index.twig", array("crestURL" => "https://login.eveonline.com/oauth/authorize?response_type=code&redirect_uri=" . $config['sso']['callbackURL'] . "&client_id=" . $config['sso']['clientID'] . "&scope=publicData&state=$inviteCode"));
});

$app->get("/manage/", function() use ($app, $config) {
    $inviteCode = "ic_" . $app->request->get("inviteCode");
    $app->render("index.twig", array("crestURL" => "https://login.eveonline.com/oauth/authorize?response_type=code&redirect_uri=" . $config['sso']['callbackURL'] . "&client_id=" . $config['sso']['clientID'] . "&scope=publicData&state=$inviteCode"));
});

$app->get("/auth/", function() use ($app, $config) {s
    $code = $app->request->get("code");
    $state = $app->request->get("state");

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

    $

    // Generate an auth string
    $authString = uniqid();

    $app->render("authed.twig", array("inviteLink" => $inviteLink, "authString" => $authString));
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
