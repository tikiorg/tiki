<?php
/* FAQ module for wollabot */

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

/* FAQ Module v. 1.0 - Thomas Johansson (prencher@prencher.dk)
 * FAQ Module v. 2.0 - mose (mose@mose.com)
 */

/* Plugin information */

class faq extends Wollabot_Module {

	function faq() {
		$this->bind_prefix('tell_faq','!faq');
		$this->bind_prefix('learn_faq','!learn');
		$this->bind_prefix('forget_faq','!forget');
	}

	function forget_faq($params) {
		$who = $params["nick"];
		$target = $params["channel"];
		if ($this->is_op($target, $who)) {
			array_shift($params["message_exploded"]);
			$item = array_shift($params["message_exploded"]);
			$def = implode(" ", $params["message_exploded"]);
			if (is_file("modules/faq.$target.txt")) {
				$file = file("modules/faq.$target.txt");
				$fp = fopen("modules/faq.$target.txt", 'w');
				foreach($file as $line) {
					if (substr($line,0,strlen($item)) != $item) fputs($fp, $line);
				}
				fclose($fp);
			}
			$this->send_privmsg($who,"Forgot from $target: '$item'");
			$this->wollabot->print_log("FAQ Module: $who removed '$item' from $target.");
		}
	}

	function learn_faq($params) {
		$who = $params["nick"];
		$target = $params["channel"];
		if ($this->is_op($target, $who)) {
			array_shift($params["message_exploded"]);
			$item = array_shift($params["message_exploded"]);
			$def  = implode(" ", $params["message_exploded"]);
			$fp = fopen("modules/faq.$target.txt", 'a');
			fputs($fp,"\n".$item.":::".$def);
			fclose($fp);
			$this->send_privmsg($who,"Learned in $target: '$item'");
			$this->wollabot->print_log("FAQ Module: $who added '$item' in $target.");
		} 
	}

	function tell_faq($params) {
		if (substr($params['channel'],0,1) == '#') {
			$target = $params['channel'];
			$who = $params['nick'].': ';
			$faq = "modules/faq.$target.txt";
		} else {
			$target = $params['nick'];
			$who = '';
			$faq = "modules/faq.txt";
		}
		array_shift($params["message_exploded"]);
		$item = array_shift($params["message_exploded"]);
		$found = false;
		$fp = fopen($faq, 'r');
		if ($fp) {
			while (!feof($fp)) {
				$output = explode(":::",fgets($fp,10000));
				if ($output[0] == $item) {
					$this->send_privmsg($target, $who."$item = ".$output[1]);
					$found = true;
					break;
				}
			}
		}
		fclose($fp);
		if ($found) {
			$this->wollabot->print_log("FAQ Module: Found '$item' for $who'$target'");
		} else {
			$this->send_privmsg($target, $who."I dont know what that is.");
			$this->wollabot->print_log("FAQ Module: Not found '$item' for $who'$target'");
		}
	}

}
$wollabot->register_module("faq");

?>
