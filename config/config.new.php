<?php
$config = array();

// CREST
$config["sso"] = array(
    "clientID" => "", // https://developers.eveonline.com/
    "secretKey" => "",
    "callbackURL" => "", // Include trailing / (Will be the url_to_the_index.com/auth/)
);

$config["db"] = array(
    "url" => "",
    "user" => "",
    "pass" => "",
    "dbname" => ""
);

// Site IGNORE EVERYTHING BELOW THIS LINE
$config["site"] = array(
    "debug" => true,
    "userAgent" => null, // Use pre-defined user agents
    "apiRequestsPrMinute" => 1800,
);

// Cookies
$config["cookies"] = array(
    "name" => "rena",
    "ssl" => true,
    "time" => (3600 * 24 * 30),
    "secret" => "",
);

// Slim
$config["slim"] = array(
    "mode" => $config["site"]["debug"] ? "development" : "production",
    "debug" => $config["site"]["debug"],
    "cookies.secret_key" => $config["cookies"]["secret"],
    "templates.path" => BASEDIR . "/view/",
);

