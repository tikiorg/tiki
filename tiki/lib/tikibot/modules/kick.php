<?php
/* Kick module for wollabot */

/*
 * Copyright (C) 2002 Mattias Pfeiffer (madd@madd.dk)
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

$loaded_modules[basename(__FILE__)] = array(
              "title"           => "Kick Module v. 0.2",
              "author"          => "Mattias Pfeiffer (madd@madd.dk)",
              "function_prefix" => "!kick",
              "function_name"   => "kick"
					    );
							
function kick($params,$target) {
	global $conf;
	if ((!strstr($target, "#")) && ($params[0] == $conf["password"])) {
		$msg = str_replace($params[0]." ".$params[1]." ".$params[2],"",implode(" ",$params));
		if($params[1]{0} != "#") {
			$params[1] = "#".$params[1];
		}
		send("KICK ".$params[1]." ".$params[2]." :".$msg);
		printlog("Kicked: '".$params[2]."' from channel ".$params[1]." with message '".$msg."'");
	}
}
?>