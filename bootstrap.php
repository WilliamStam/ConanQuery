<?php

namespace {
	$DebugLevel = 4;
	$mtime = microtime();
	$mtime = explode(" ", $mtime);
	$GLOBALS['page_timer_start'] = $mtime[1] + $mtime[0];

	date_default_timezone_set('Africa/Johannesburg');
	setlocale(LC_ALL, 'en_ZA.UTF8');


	$errorFolder = __DIR__ . DIRECTORY_SEPARATOR . "logs" . DIRECTORY_SEPARATOR . "php";
	if ( !file_exists($errorFolder) ) {
		@mkdir($errorFolder, 01777, TRUE);
	}
	$errorFile = $errorFolder . DIRECTORY_SEPARATOR . date("Y-m") . ".log";
	ini_set("error_log", $errorFile);

	if ( session_id() == "" ) {
		$SID = @session_start();
	} else {
		$SID = session_id();
	}
	if ( !$SID ) {
		session_start();
		$SID = session_id();
	}

	if ( !isset($_SESSION['initiated']) ) {
		session_regenerate_id();
		$_SESSION['initiated'] = TRUE;
		$SID = session_id();
	}


	require('inc/functions.php');
	require('inc/timer.php');


	require_once('vendor/autoload.php');


	$cfg = array();
	require('config.default.inc.php');
	if ( file_exists(__DIR__ . DIRECTORY_SEPARATOR . "config.inc.php") ) {
		require('config.inc.php');
	}

	$version = date("YmdH");
	if ( file_exists("./.git/refs/heads/" . $cfg['git']['branch']) ) {
		$version = file_get_contents("./.git/refs/heads/" . $cfg['git']['branch']);
	}
	$minVersion = numberHash($version);


	if ( defined("CRYPT_BLOWFISH") && CRYPT_BLOWFISH ) {

	} else {
		echo "CRYPT_BLOWFISH is not available";
		exit();
	}


	/* if the testing client connects ALWAYS go into max debug mode */


	/* Setting up F3 Variables */

	$f3 = \Base::instance();
	$f3->set("DEBUG", isset($DebugLevel) ? $DebugLevel : 0);
	$f3->set("CFG", $cfg);
	$f3->set("PACKAGE", $cfg['POWERED-BY']);
	$f3->set("VERSION", $minVersion);
	$f3->set('DB', new DB\SQL('mysql:host=' . $cfg['DB']['host'] . ';dbname=' . $cfg['DB']['database'] . '', $cfg['DB']['username'], $cfg['DB']['password'],array( \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION )));

	$f3->set('AUTOLOAD', './|lib/|inc/|/controllers/*');
	$f3->set('PLUGINS', 'vendor/bcosca/fatfree/lib/');
	$f3->set('CACHE', FALSE);

	$f3->set('UI', '/templates/|/views/');
	$f3->set('MEDIA', './media/|' . $cfg['media']);
	$f3->set('TAGS', 'p,br,b,strong,i,italics,em,h1,h2,h3,h4,h5,h6,div,span,blockquote,pre,cite,ol,li,ul');
	$f3->set('ERROR_LOG', $errorFile);
	$f3->set('SID', $SID);


	/*

	$f3->set("ONERROR",function($f3) {
		$error = $f3->get('ERROR');


		$e = $f3->get('EXCEPTION');
		// There isn't an exception when calling `Base->error()`.
		if (!$e instanceof Throwable) {
			$e = new Exception('HTTP ' . $f3->get('ERROR.code'));
		}



	});
	*/

	/* Setting up Application */

	$f3->set('OUTPUT', array());


}

namespace bootstrap {
	class app {
		public function __construct() {
			$this->f3 = \Base::instance();


		}

		public function __destruct() {


		}

		function run() {


			$this->f3->run();


			$mtime = microtime();
			$mtime = explode(" ", $mtime);
			$mtime = $mtime[1] + $mtime[0];

			$this->f3->OUTPUT['MEMORY'] = (memory_get_peak_usage(TRUE));
			$this->f3->OUTPUT['TIME'] = \timer::shortenTimer($mtime - $GLOBALS['page_timer_start']);

			$url = "http" . (($_SERVER['SERVER_PORT'] == 443) ? "s://" : "://") . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
			$this->f3->OUTPUT['DEBUG']['page'] = array(
				"uri" => $_SERVER['REQUEST_URI'],
				"url" => $url,
				"time" => $this->f3->OUTPUT['TIME'],
				"memory" => $this->f3->OUTPUT['MEMORY'],
			);


			if ( isset($_GET['raw']) ) {
				debug($this->f3->OUTPUT);
			}

			$thispagedebug = $this->f3->OUTPUT['DEBUG'];
			$this->f3->OUTPUT['DEBUG'] = (array)$GLOBALS['DEBUG'];
			$this->f3->OUTPUT['DEBUG'][] = $thispagedebug;
			//echo \app\views\renderer::getInstance($this->f3->OUTPUT)->output($this->f3->get("PARAMS['FORMAT']"));


			$this->render();


		}


		function render() {
			$data = $this->f3->OUTPUT;
			$order = array(
				"STATUS",
				"MSG",
				"DATA",
				"TIME",
				"MEMORY",
				"DEBUG",
				"DEBUG_API",
			);



			uksort($data, function($key1, $key2) use ($order) {
				return (array_search($key1, $order) > array_search($key2, $order));
			});


			if ( isset($data['RENDERED']) ) {
				$timerStr = "";
				if ( isset($this->f3->OUTPUT['DEBUG']) ) {
					$timer = json_encode($this->f3->OUTPUT['DEBUG']);
					$timerStr = <<<Timer
<script>
$("#debugger").jqotesub($("#template-debugger"), $timer);

</script>
Timer;
				}

				echo str_replace('<!-- TIMERS -->', $timerStr, $data['RENDERED']);
			} else {
				header("Content-Type: application/json");

				echo json_encode($data);


			}


		}
	}
}

