<?php
/* PHP function module for Wollabot */

/*
 * Copyright (C) 2002 Christian Joergensen (mail@phpguru.dk)
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

class phpfunctions extends Wollabot_Module {
	
	function phpfunctions() {
		$this->bind_prefix('php_lookup','!php');
	}


/*
$wallabot->loaded_modules[basename(__FILE__)] = array("title"           => "PHP function prototype lookup v. 0.1",
					    "author"          => "Christian Joergensen (mail@phpguru.dk)",
					    "function_prefix" => "!php",
					    "function_name"   => "php_lookup"
					    );
*/
	function php_lookup($params) {
		$request = $params["message_exploded"][1];
		if (substr($params['channel'],0,1) == '#') {
			$target = $params['channel'];
			$who = $params["nick"].': ';
		} else {
			$target = $params["nick"];
			$who = '';
		}
		$this->wollabot->print_log("PHP: Looking up '$request' for $who'$target'");

		$fp = fopen("modules/php.def", 'r');
		while (!feof($fp)) {
			$buf = fgets($fp, 10000);
			if (substr($buf,0,strpos($buf,'(')) == $request) {
				ereg("^(.+)\((.+)\) (.+)$", $buf, $regs);
				$this->send_privmsg($target, $who.$regs[1]." (".$regs[2].") - ".trim($regs[3])." - http://php.net/".$regs[1]);
				return;
			}
		}
		$this->send_privmsg($target, $who."No such PHP function $request");
	}
}

$wollabot->register_module("phpfunctions");

?>
