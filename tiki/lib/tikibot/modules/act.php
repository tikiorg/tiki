<?php
/* Act module for wollabot */

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

class act extends Wollabot_Module {

function act() {
	$this->bind_prefix('perform_act','!act');
}

function perform_act($params) {
	$target = $params['channel'];
	$who = $params['nick'];
	array_shift($params["message_exploded"]);
	$where = array_shift($params["message_exploded"]);
	$param = implode(" ", $params["message_exploded"]);
	
  if ((!strstr($target, "#")) && ($this->is_op($where, $who)) && $param) {
    $this->wollabot->print_log("$who Act '".$param."' to '".$where."'");
		$this->send_action($where, $param);
  }
}
}

$wollabot->register_module("act");

?>
