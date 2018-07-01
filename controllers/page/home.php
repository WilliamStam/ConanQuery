<?php
namespace controllers\page;
use xPaw\SourceQuery\SourceQuery;
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




        define( 'SQ_SERVER_ADDR', $host );
        define( 'SQ_SERVER_PORT', $port );
        define( 'SQ_TIMEOUT',     $timeout );
        define( 'SQ_ENGINE',      SourceQuery::SOURCE );
        define( 'SQ_PASSWORD',      $password );
        // Edit this <-

        $Query = new SourceQuery( );

        try {
            $Query->Connect( SQ_SERVER_ADDR, SQ_SERVER_PORT, SQ_TIMEOUT, SQ_ENGINE );

            $Query->SetRconPassword( SQ_PASSWORD );

            debug($Query->Rcon("sql Select  (g.name) as GUILD,  (c.char_name) as NAME,  'TeleportPlayer '||ap.x||' '||ap.y||' '||ap.z as LOCATION, ap.x, ap.y, ap.y, datetime(c.lastTimeOnline, 'unixepoch') as LASTONLINE from characters as c  left outer join guilds as g on g.guildid = c.guild
  left outer join actor_position as ap on ap.id = c.id where  lastTimeOnline > strftime('%s', 'now', '-5 minute') order by   g.name,   c.rank desc,   c.level desc,   c.char_name;"));
            debug($Query->Rcon( 'listplayers' ));
            //var_dump( $Query->Rcon( 'say hello' ) );
        } catch( Exception $e ) {
            echo $e->getMessage( );
        }
        finally
        {
            $Query->Disconnect( );
        }



        exit();

		$tmpl = new \template("main.twig","templates/");
		$tmpl->page = array(
			"title"=> "Dashboard",
		);
		$tmpl->renderPage($this,__FUNCTION__);




		$return['time'] = $timer->_stop('Controllers - PAGE');
		return $this->f3->OUTPUT['RESPONSE'] = $return;
	}

}
