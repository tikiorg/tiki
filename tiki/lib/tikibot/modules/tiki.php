<?php
/* Tiki module for Wollabot */

/*
 * Copyright (C) 2003 by mose <mose@feu.org>
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 * 
 */

/* Plugin information */

class tiki extends Wollabot_Module {
	
	function tiki() {
		$this->bind_prefix('tiki_do','!T');
	}

	function tiki_do($params) {

		if (substr($params['channel'],0,1) == '#') {
			$target = $params['channel'];
			$who = $params["nick"].": ";
		} else {
			$target = $params["nick"];
			$who = '';
		}
		array_shift($params["message_exploded"]);
		$command = array_shift($params["message_exploded"]);
		$arg = array_shift($params["message_exploded"]);
		$args = $params["message_exploded"];
		
		require_once("/usr/local/tikiwiki/db/local.php");
		// require_once("/usr/local/tikiwiki/lib/tikilib.php");
		
		$conn = mysql_connect($host_tiki, $user_tiki, $pass_tiki);
		mysql_select_db($dbs_tiki,$conn);
		
		// $T = new TikiLib($conn);
		
		switch ($command) {
			
			case 'stats':
				if (!$arg or $arg > 250) $arg = 1;
				$since = time() - ($arg*60*60);
				$query = "select count(*) as c from tiki_history where lastModif > $since";
				$res = @mysql_query($query,$conn);
				if ($res) {
					$hits = mysql_result($res,0,'c');
				}
				if ($hits < 1) $hits = 'no';
				$this->send_privmsg($target, $who."$hits wiki pages changed in the last $arg hours on tw.o");
				break;
			
		case 'who':	
			$query = "select `user` from `tiki_sessions` where `user`<>''";
			$res = @mysql_query($query,$conn);
			$here = array();
			if ($res) {
				for($i=0; $row = mysql_fetch_array($res); $i++) {
					$here[] = $row['user'];
				}
			}
			if (count($here)) {
				$this->send_privmsg($target, $who."$i are there : ".implode(', ',$here)." on tw.o");
			} else {
				$this->send_privmsg($target, $who."There is nobody on tw.o !");
			}
			break;
			
			default :
				$this->send_privmsg($target, $who."use a command like 'stat', or 'who'");
		}
		
		mysql_close($conn);
	}
}

$wollabot->register_module("tiki");

?>
