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

class amiop extends Wollabot_Module {
	
	function amiop() {
		$this->bind_prefix('ami','!amiop');
	}


/*
$wallabot->loaded_modules[basename(__FILE__)] = array("title"           => "PHP function prototype lookup v. 0.1",
					    "author"          => "Christian Joergensen (mail@phpguru.dk)",
					    "function_prefix" => "!php",
					    "function_name"   => "php_lookup"
					    );
*/
	function ami($params) {
		$who = $params["nick"];
		$target = $params["channel"];
		if ($this->is_op($target,$who)) {
		$this->send_privmsg($target, "$who: you are OP on channel $target");
		} else {
		$this->send_privmsg($target, "$who: you are not op on channel $target");
		}
	}
}

$wollabot->register_module("amiop");

?>
