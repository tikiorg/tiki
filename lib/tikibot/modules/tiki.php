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
		$this->bind_prefix('tiki_do','!T ');
	}

	function tiki_do($params) {
		global $tikilib;

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
		
		switch ($command) {
			case 'rpage':
				list($page) = $tikilib->get_random_pages("1");
				$this->send_privmsg($target, $who."Want a page . try that one : http://tikiwiki.org/tiki-index.php?page=$page");
				break;
			
			case 'who':
				$users = $tikilib->get_online_users();
				foreach ($users as $u) {
					$all[] = $u['user'];
				}
				$this->send_privmsg($target, $who."There is ".count($users)." users online right now on tw.o (".implode(', ',$all).")");
				break;

			case 'stats':
				global $statslib;
				$i = $statslib->site_stats();
				$this->send_privmsg($target, $who."Since ".date("Y-m-d",$i["started"])." we got ".$i["pageviews"]." page viewed on tw.o.");
				break;

			default :
				$this->send_privmsg($target, $who."use a command like 'who', 'stats' or 'rpage'");
		}
		
	}
}

$wollabot->register_module("tiki");

?>
