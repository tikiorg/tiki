<?php
/* fortune-script for Wollabot */

/*
 * Copyright (C) 2003 mose (mose@feu.org)
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

class fortune extends Wollabot_Module {

	function fortune() {
		$this->bind_prefix('fortune_talk','!Fe');
		$this->bind_prefix('fortune_fr','!Fr');
	}

	function fortune_fr($params) {

		$param = implode(" ", $params["message_exploded"]);
		if (substr($params['channel'],0,1) == '#') {
			$target = $params['channel'];
			$who = $params["nick"].": ";
		} else {
			$target = $params["nick"];
			$who = '';
		}

		$fortune = str_replace("\n"," ",`/usr/games/fortune -es fr`);

		$this->send_privmsg($target, $who.$fortune);
		$this->wollabot->print_log("FORTUNE: blattered for $who'$target'");
	}

	function fortune_talk($params) {

		$param = implode(" ", $params["message_exploded"]);
		if (substr($params['channel'],0,1) == '#') {
			$target = $params['channel'];
			$who = $params["nick"].": ";
		} else {
			$target = $params["nick"];
			$who = '';
		}

		$fortune = str_replace("\n"," ",`/usr/games/fortune -es linux education literature bofh-excuses`);

		$this->send_privmsg($target, $who.$fortune);
		$this->wollabot->print_log("FORTUNE: blattered for $who'$target'");
	}
}

$wollabot->register_module("fortune");

?>
