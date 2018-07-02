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


            $sql = "
SELECT  
	'' AS num,
	(c.char_name) AS NAME,  
	(SELECT name FROM guilds WHERE guilds.guildId = c.guild) AS GUILD,
	(c.guild) AS GUILDID,  
	('TeleportPlayer '||ap.x||' '||ap.y||' '||ap.z) AS LOCATION, 
	(ap.x), 
	(ap.y), 
	(ap.y), 
	(datetime(c.lastTimeOnline, 'unixepoch')) AS LASTONLINE 
FROM 
	characters AS c  
  		LEFT OUTER JOIN actor_position AS ap ON ap.id = c.id 
WHERE 
	lastTimeOnline > strftime('%s', 'now', '-5 minutes'); 
  ";

            $query = $Query->Rcon("sql {$sql}");


            $query = $this->QueryParser($query);




            debug($query);
            //debug($Query->Rcon( 'listplayers' ));
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

	function QueryParser($data){
		$d = array();
		$data = explode("\n",$data);
		$columns = array_shift($data);
		$cols = array();
		foreach (explode("|",$columns) as $item_c){
			$cols[] = trim($item_c);
		}
		array_pop($cols);
		$columns = $cols;



		$i = 0;
		foreach ($data as $item){
			$c = array();
			$cl=0;
			foreach (explode("|",$item) as $item_c){
				$c[$columns[$cl++]] = trim($item_c);
			}


			array_pop($c);

			$d[] = $c;
		}
		return $d;
	}


}
