<?php


ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL | E_STRICT);



require_once __DIR__ . '/vendor/autoload.php';

require_once("/var/www/ntk-vitaever-spid/proxy-spid-php.php");
require_once("/var/www/ntk-vitaever-spid/lib/ResponseHandler.php");
require_once("/var/www/ntk-vitaever-spid/lib/ResponseHandlerPlain.php");
require_once("/var/www/ntk-vitaever-spid/lib/ResponseHandlerSign.php");
require_once("/var/www/ntk-vitaever-spid/lib/ResponseHandlerSignEncrypt.php");
require_once("/var/www/ntk-vitaever-spid/lib/ResponseHandlerEncryptSign.php");


$service = "spid";
$sspSession = \SimpleSAML\Session::getSessionFromRequest();
$sspSession->doLogout($service);
echo "fattos?";
die;



