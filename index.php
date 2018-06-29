<?php
$DebugLevel = 4;

require_once('bootstrap.php');




$f3->route('GET /', 'controllers\page\home->page');


$f3->route('GET /heartbeat', function($f3, $params) {

	echo date("Y-m-d H:i:s");
	exit();

});


$f3->route('GET /update', function($f3, $params) {

	$f3->reroute("/Updater/index.php");

});
$f3->route('GET /php', function($f3, $params) {

	echo phpinfo();
	exit();

});
(new bootstrap\app())->run();


?>
