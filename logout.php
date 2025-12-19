<?php

require_once("spid-php.php");

$production = false;
$service = "spid";
$spidsdk = new SPID_PHP($production, "spid");

$redirect_url = isset($_GET['redirect_url']) ? $_GET['redirect_url'] : "";

if ($spidsdk->isAuthenticated() && $redirect_url != "") {
    $spidsdk->logout($redirect_url);
} else {
    //qualcosa è andato storto, redirigo su Vitaever
    if ($redirect_url) {
        header("Location: $redirect_url");
    } else {
        header("Location: https://vitaever.com");
    }
}
exit;
?>
