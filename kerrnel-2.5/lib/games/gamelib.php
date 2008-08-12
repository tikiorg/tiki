<?php

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

class GameLib extends TikiLib {
	function GameLib($db) {
		$this->TikiLib($db);
	}

	function add_game_hit($game) {
		global $prefs, $user;

		if ($prefs['count_admin_pvs'] == 'y' || $user != 'admin') {
			$cant = $this->getOne("select count(*) from `tiki_games` where `gameName`=?",array($game));

			if ($cant) {
				$query = "update `tiki_games` set `hits` = `hits`+1 where `gameName`=?";
				$bindvars=array($game);
			} else {
				$query = "insert into `tiki_games`(`gameName`,`hits`,`points`,`votes`) values(?,?,?,?)";
				$bindvars=array($game,1,0,0);
			}

			$result = $this->query($query,$bindvars);
		}
	}

	function get_game_hits($game) {
		$cant = $this->getOne("select count(*) from `tiki_games` where `gameName`=?",array($game));

		if ($cant) {
			$hits = $this->getOne("select `hits` from `tiki_games` where `gameName`=?",array($game));
		} else {
			$hits = 0;
		}

		return $hits;
	}
}
global $dbTiki;
$gamelib = new GameLib($dbTiki);

?>
