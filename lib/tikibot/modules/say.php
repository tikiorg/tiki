<?php
/* Say module for wollabot */

/*
 * Copyright (C) 2002 Thomas Johansson (prencher@prencher.dk)
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

class say extends Wollabot_Module {

	function say() {
		$this->bind_prefix('perform_say','!say');
	} 
	/*
	$loaded_modules[basename(__FILE__)] = array(
								"title"           => "Say Module v. 1.0",
								"author"          => "Thomas Johansson (prencher@prencher.dk)",
								"function_prefix" => "!say",
								"function_name"   => "say"
								);
	*/
	function perform_say($params) {
		$target = $params['channel'];
		$who = $params['nick'];
		array_shift($params["message_exploded"]);
		$where = array_shift($params["message_exploded"]);
		$param = implode(" ", $params["message_exploded"]);

		if ((strstr($target, "#")) && ($this->is_op($target, $who)) && $param) {
			$this->send_privmsg($where,$param);
			$this->wollabot->print_log("$who Said '".$param."' to '".$where."'");
		}
	}
}

$wollabot->register_module("say");

?>
