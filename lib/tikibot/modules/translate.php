<?php
/* Translation module (using http://babelfish.altavista.com) for Wollabot */
/* WARNING WARNING WARNING. Requires curl support in PHP. */
/*
 * Copyright (C) 2002 Dan Kuykendall (dan@kuykendall.org)
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
 * Foundation, Inc., 59 Temple Place for Suite 330, Boston, MA  02111-1307, USA.
 * 
 */

/* Plugin information */

class translate extends Wollabot_Module {
var $langs = array();

function translate() {
	$this->bind_prefix('trans','!B');
	$this->langs = array('en_zh','en_fr','en_de','en_it','en_ja','en_ko','en_pt','en_es','zh_en','fr_en','fr_de','de_en','de_fr','it_en','ja_en','ko_en','pt_en','ru_en','es_en');
}
/*
$loaded_modules[basename(__FILE__)] = array("title"           => "Translation v. 1.0",
					    "author"          => "Dan Kuykendall (dan@kuykendall.org)",
					    "function_prefix" => "!translate",
					    "function_name"   => "translate"
					    );
*/
function trans($params) {
	if (substr($params['channel'],0,1) == '#') {
		$target = $params['channel'];
		$who = $params["nick"].": ";
	} else {
		$target = $params["nick"];
		$who = '';
	}
	array_shift($params["message_exploded"]);
	$lang = array_shift($params["message_exploded"]);
	$param = implode(" ", $params["message_exploded"]);
	
	if(!function_exists('curl_init')) {
		$this->wollabot->print_log('TRANSLATE: Requires curl support to work.');
   	$this->send_privmsg($target, 'TRANSLATE: Not fully enabled. See log for details.');
		return;
	}
	
	if (!in_array($lang,$this->langs)) {
		$this->send_privmsg($target, 'TRANSLATE: language not available check http://tikiwiki.org/TikiBot for a correct list.');
		return;
	}
	if($param and $lang) {
		$buffer = '';
		$str = 'tt=urltext&urltext='.urlencode($param).'&lp='.$lang.'&submit=Translate';
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL,"http://babelfish.altavista.com/babelfish/tr");
		curl_setopt($ch, CURLOPT_FAILONERROR, 1);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch, CURLOPT_TIMEOUT, 3);
		curl_setopt ($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $str);
		ob_start();
		curl_exec ($ch);
		$buffer = ob_get_contents();
		ob_end_clean();
		curl_close ($ch);
		$start = strpos ($buffer, 'padding:10px')+14;
		$buffer = substr($buffer, $start);
		$end = strpos ($buffer, '</div>');
		$buffer = trim(substr($buffer, 0, $end));
		if (strstr($buffer,"The translation server is currently unavailable")) {
			$this->wollabot->print_log("TRANSLATE: BabelFish unavalaible");
			$this->send_privmsg($target, $who.'Sorry, BabelFish is down');
		} elseif(!empty($buffer)) {
			$this->wollabot->print_log("TRANSLATE: Translating '$param' with '$lang' for $who'$target'");
    	$this->send_privmsg($target, $who.'('.$lang.') '.$param." = ".$buffer);
		} else {
    	$this->send_privmsg($target, $who.'There was an error translating your text.');			
		}
	} else {
	  $this->send_privmsg($target, $who.'TRANSLATE HELP: Usage: !B <language code> text.');
	}
}
}

$wollabot->register_module("translate");

?>
