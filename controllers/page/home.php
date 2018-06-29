<?php
namespace controllers\page;
use Thedudeguy\Rcon as rcon;
class home extends \controllers\_ {
	public function __construct() {
		parent::__construct();
		$this->data = array();


	}
	public function __destruct() {


	}
	function page(){
		$timer = new \timer();
		$return = array();

		$host = $this->f3->get("CFG")['rcon']['host']; // Server host name or IP
		$port = $this->f3->get("CFG")['rcon']['port'];                      // Port rcon is listening on
		$password = $this->f3->get("CFG")['rcon']['password']; // rcon.password setting set in server.properties
		$timeout = $this->f3->get("CFG")['rcon']['timeout'];                       // How long to timeout.



		$rcon = new rcon($host, $port, $password, $timeout);

		if ($rcon->connect()) {

			debug($rcon->sendCommand("listplayers"));
		}
		debug($rcon);



		$tmpl = new \template("main.twig","templates/");
		$tmpl->page = array(
			"title"=> "Dashboard",
		);
		$tmpl->renderPage($this,__FUNCTION__);




		$return['time'] = $timer->_stop('Controllers - PAGE');
		return $this->f3->OUTPUT['RESPONSE'] = $return;
	}

}
