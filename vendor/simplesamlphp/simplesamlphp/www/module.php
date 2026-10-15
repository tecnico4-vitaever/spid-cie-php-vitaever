<?php

/**
 * This web page receives requests for web-pages hosted by modules, and directs them to
 * the process() handler in the Module class.
 */
$_SERVER['SERVER_PORT']=$_SERVER['HTTP_X_FORWARDED_PORT']?:$_SERVER['SERVER_PORT'];
$_SERVER['REQUEST_SCHEME']=$_SERVER['HTTP_X_FORWARDED_PROTO']?:$_SERVER['REQUEST_SCHEME'];
if($_SERVER['HTTP_X_FORWARDED_PROTO']=='https')
	$_SERVER['HTTPS']='on';

require_once('_include.php');

\SimpleSAML\Module::process()->send();
