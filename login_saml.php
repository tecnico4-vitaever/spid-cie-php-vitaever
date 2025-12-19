<?php

/*
 * ini_set('display_errors', 1);
 * ini_set('display_startup_errors', 1);
 * error_reporting(E_ALL | E_STRICT);
 */


require_once __DIR__ . '/vendor/autoload.php';

require_once("/var/www/ntk-vitaever-spid/proxy-spid-php.php");
require_once("/var/www/ntk-vitaever-spid/lib/ResponseHandler.php");
require_once("/var/www/ntk-vitaever-spid/lib/ResponseHandlerPlain.php");
require_once("/var/www/ntk-vitaever-spid/lib/ResponseHandlerSign.php");
require_once("/var/www/ntk-vitaever-spid/lib/ResponseHandlerSignEncrypt.php");
require_once("/var/www/ntk-vitaever-spid/lib/ResponseHandlerEncryptSign.php");


use Firebase\JWT\JWT;

const PROXY_CONFIG_FILE = "/var/www/ntk-vitaever-spid/spid-php-proxy.json";
const TOKEN_PRIVATE_SPID_KEY = "/var/www/ntk-vitaever-spid/cert/spid-sp.pem";
const TOKEN_PUBLIC_SPID_CERT = "/var/www/ntk-vitaever-spid/cert/spid-sp.crt";
const TOKEN_PRIVATE_CIE_KEY = "/var/www/ntk-vitaever-spid/cert/cie-sp.pem";
const TOKEN_PUBLIC_CIE_CERT = "/var/www/ntk-vitaever-spid/cert/cie-sp.crt";
const DEFAULT_SPID_LEVEL = 2;
const DEFAULT_CIE_LEVEL = 3;
const DEFAULT_ATCS_INDEX = null;    // set to null to retrieve it from metadata
const DEFAULT_EIDAS_ATCS_INDEX = 100;

const DEBUG = false;

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$proxy_config = file_exists(PROXY_CONFIG_FILE)? json_decode(file_get_contents(PROXY_CONFIG_FILE), true) : array();
$production = $proxy_config['production'];

$clients        = $proxy_config['clients'];

$client_id = $_ENV['CLIENT_ID'];
$shared_secret = $_ENV['SHARED_SECRET'];

$idp            = $_GET['idp'];

$spidcie_level  = $clients[$client_id]['level'];
if($spidcie_level===null || !in_array($spidcie_level, [1,2,3])) $spidcie_level = $isCIE? DEFAULT_CIE_LEVEL : DEFAULT_SPID_LEVEL;

$atcs_index     = $clients[$client_id]['atcs_index'];
if($atcs_index===null || !is_numeric($atcs_index)) $atcs_index = DEFAULT_ATCS_INDEX;
if($idp=="EIDAS" || $idp=="EIDAS QA") $atcs_index = DEFAULT_EIDAS_ATCS_INDEX;



if(in_array($client_id, array_keys($clients)) && (isset($_GET['redirect_uri']) || isset($_GET['redirect_to_app']))) {

    $redirect_to_app = false;
    $redirect_uri = "";

    if (isset($_GET['redirect_uri'])) {
        $redirect_uri = $_GET['redirect_uri'];
    } else {
        $redirect_to_app = true;
    }

    $service = "spid";

    if (isset($clients[$client_id]['service'])) {
        $service = $clients[$client_id]['service'];
    }

    $isCIE = ($idp == "CIE" || $idp == "CIE TEST");
    $service = $isCIE ? "cie" : $service;

    $spidsdk = new SPID_PHP($production, $service);

    if (!$spidsdk->isIdPAvailable($idp)) {
        http_response_code(404);
        if (DEBUG) echo "idp not found";
        die();
    }


    if ($spidsdk->isAuthenticated()
        && isset($_GET['idp'])
        && $spidsdk->isIdP($_GET['idp'])) {

        // dearray values
        $data = array();
        foreach ($spidsdk->getAttributes() as $attribute => $value) {
            $response_attributes_prefix = $proxy_config['clients'][$client_id]['response_attributes_prefix'];
            $response_attributes_prefix = $response_attributes_prefix ? $response_attributes_prefix : '';
            $data[$response_attributes_prefix . $attribute] = $value[0];
        }

        $client_config = $proxy_config['clients'][$client_id];
        $handlerClass = 'ResponseHandler' . $client_config['handler'];

        if (!in_array($handlerClass, [
            'ResponseHandlerPlain',
            'ResponseHandlerSign',
            'ResponseHandlerSignEncrypt',
            'ResponseHandlerEncryptSign'
        ])) {
            if ($proxy_config['signProxyResponse']) {
                if ($proxy_config['encryptProxyResponse']) {
                    $handlerClass = 'ResponseHandlerEncryptSign';
                } else {
                    $handlerClass = 'ResponseHandlerSign';
                }
            } else {
                $handlerClass = 'ResponseHandlerPlain';
            }
        }
        /*
        $handler = new $handlerClass($proxy_config['spDomain'], $client_config);
        $handler->set('providerId', $spidsdk->getIdP());
        $handler->set('providerName', $spidsdk->getIdPKey());
        $handler->set('responseId', $spidsdk->getResponseID());
        $handler->set('privateKey', $isCIE ? TOKEN_PRIVATE_CIE_KEY : TOKEN_PRIVATE_SPID_KEY);
        $handler->set('publicCert', $isCIE ? TOKEN_PUBLIC_CIE_CERT : TOKEN_PUBLIC_SPID_CERT);
        */

        $sspSession = \SimpleSAML\Session::getSessionFromRequest();
        $sspSession->doLogout($service);

        $payload = [
            "fiscal_number" => $data['fiscalNumber'],
            "email" => $data['email'],
        ];

        $jwt = JWT::encode($payload, $shared_secret, 'HS256');

        $isSpidLogin = $spidsdk->isCIE() ? false : true;

        if (!$redirect_to_app) {
            header("Location: $redirect_uri?token=" . urlencode($jwt) . "&is_spid_login=" . $isSpidLogin);
        } else {
            header("Location: ntk-app://MYHOST#/-/spidcie?token=" . urlencode($jwt) . "&is_spid_login=" . $isSpidLogin);
        }

        die();

    } else {
        $returnTo = $_SERVER['SCRIPT_URI'].'?idp='.$idp;
        if ($redirect_uri != "") {
            $returnTo .= '&redirect_uri='.$redirect_uri;
        }
        setcookie('SPIDPHP_PROXYRETURNTO', $returnTo, time()+60*5, '/');
        $spidsdk->login($idp, $spidcie_level, $_SERVER['SCRIPT_URI'], $atcs_index);
        die();
    }
} else {
    http_response_code(400);
    if(DEBUG) echo "action not valid";
    die();
}

?>