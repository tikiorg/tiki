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
		$this->bind_prefix('tiki_do','!tiki ');
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
		$method = "tiki_$command";
		$arg = array_shift($params["message_exploded"]);
		$args = $params["message_exploded"];
		
		if (!is_callable('tiki',$method)) {
			$method = 'tiki_help';
		}
		$back = $this->$method();
		$this->wollabot->print_log("$who asks $command on $target and gets $back");
		$this->send_privmsg($target, $who.$back);
	}


	function tiki_rpage() {
		global $tikilib;
		list($page) = $tikilib->get_random_pages("1");
		return "Want a page ? Try that one : http://tikiwiki.org/$page !";
	}

	function tiki_who() {
		global $tikilib;
		$users = $tikilib->get_online_users();
		foreach ($users as $u) {
			$all[] = $u['user'];
		}
		$count = count($users);
		if ($count == 0) {
			return "There is nobody known right now on tw.o";			 
		} elseif ($count == 1) {
			return "There is someone right now on tw.o (".$all[0].")";			 
		} else {
			return "There is ".count($users)." known users right now on tw.o (".implode(', ',$all).")";			 
		}
	}
	
	function tiki_stats() {
		global $statslib;
		$i = $statslib->site_stats();
		return "Since ".date("Y-m-d",$i["started"])." (".$i["days"]." days) we got ".$i["pageviews"]." page viewed on tw.o (".round($i["ppd"])." per day).";
	}

	function tiki_help() {
		return "help <item> for more info : who stats rpage.";
	}

}

$wollabot->register_module("tiki");

?>
