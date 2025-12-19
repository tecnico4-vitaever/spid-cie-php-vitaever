<?php

require_once("/var/www/html/spid-php.php");

use SimpleSAML\Session;

echo "test..<br><br>";

$session = Session::getSessionFromRequest();
//$session->setData('test', 'foo', 'bar');
//$session->save();  // forza il salvataggio

print_r($session->getData('test', 'foo'));
