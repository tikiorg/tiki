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
		$this->bind_prefix('tiki_do','t. ');
		$this->bind_prefix('tiki_do','!T ');
		$this->bind_prefix('tiki_do','!tiki ');
	}

	function tiki_do($params) {
		global $who;

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
		
		if (!method_exists($this,$method)) {
			$method = 'tiki_help';
		}
		$back = $this->$method($arg,$args);
		$this->wollabot->print_log("$who asks $command on $target and gets $back");
		$this->send_privmsg($target, $who.$back);
	}


	function tiki_rpage($arg,$args) {
		if ($arg == 'help') {
			return "[!T rpage] Returns a random wiki page url.";
		} else {
			global $tikilib;
			list($page) = $tikilib->get_random_pages("1");
			return "Want a page ? Try that one : http://tikiwiki.org/$page !";
		}
	}

	function tiki_whoami($arg,$args) {
		
	}
	function tiki_who($arg,$args) {
		if ($arg == 'help') {
			return "[!T who] Returns who is connected on tikiwiki.org right now.";
		} else {
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
	}
	
	function tiki_add($arg,$args) {
		if ($arg == 'help') {
			return "[!T add PageName my comment] Appends some lines on a WikiPage on tikiwiki.org.";
		} else {
			global $tikilib;
			global $who;
			$ppl = strtok($who,':');
			// if ($ppl == 'mose' or $ppl == 'damian') {
				$comment = implode(' ',$args);
				$comment = preg_replace("~((https?|ftp|irc)://)([^ ]*)~i", "[\\0]", $comment);
				$date = date("Y/m/d H:i");
				$textinput = "\n~~#666666:''__{$ppl}__ $date :''~~ $comment";
				if ($tikilib->page_exists($arg)) {
					$tikilib->invalidate_cache($arg);
					$info = $tikilib->get_page_info($arg);
					if (strpos($info['data'],"IrcHook")) {
						$data1 = substr($info['data'],0,strpos($info['data'],"IrcHook") + 7);
						$data2 = substr(strstr($info['data'],"IrcHook"),7);
						$tikilib->update_page($arg,$data1.$textinput.$data2, "added via tikibot", $ppl, '(via tikibot)', $info['description']);
						return "Added your comment on http://tikiwiki.org/$arg";
					} else {
						return "Sorry, that page has no IrcHook.";
					}
				} else {
					return "Unknown page : $arg";
				}
			//} else {
			//	return "Sorry, $ppl, I can't.";
			//}
		}
	}

	
	function tiki_stats($arg,$args) {
		if ($arg == 'help') {
			return "[!T stats] Returns statistics of usage of tikiwiki.org.";
		} else {
			global $statslib;
			$i = $statslib->site_stats();
			return "Since ".date("Y-m-d",$i["started"])." (".$i["days"]." days) we got ".$i["pageviews"]." pages viewed on tw.o (".round($i["ppd"])." per day).";
		}
	}

	function tiki_art($arg,$args) {
		if ($arg == 'help') {
			return "[!T art] Returns the title and url of last published article.";
		} else {
			global $tikilib;
			if (!isset($arg) or $arg < 0 or $arg > 20) { $arg = 0; }
			$art = $tikilib->list_articles($arg, 1, 'publishDate_desc', '', '', $this->wollabot->configuration['tikibot'], '', '');
			return "Articles [ $arg ][ ".$art['data'][0]['title']." ]:[ http://tikiwiki.org/art".$art['data'][0]['articleId']." ].";
		}
	}

	function tiki_dir($arg,$args) {
		if ($arg == 'help') {
			return "[!T dir] Returns the last added directory site";
		} else {
			global $tikilib;
			if (!isset($arg) or $arg < 0 or $arg > 20) { $arg = 0; }
			$dir = $tikilib->dir_list_all_valid_sites2($arg, 1, 'created_desc', '');
			return "Directory [ $arg ]:[ ".$dir['data'][0]['name']." ]:[ ".$dir['data'][0]['url']." ].";
		}
	}

	function tiki_find($arg,$args) {
		if ($arg == 'help') {
			return "[!T find] Finds a wiki page including string";
		} else {
			global $tikilib;
			$page = array();
			if (!isset($args[0]) or !is_int($args[0]) or $args[0] < 0 or $args[0] > 20) { $args[0] = 0; }
			$page = $tikilib->list_pages($args[0], 1, 'lastModif_desc', "$arg");
			if ($page['cant'] > 0) {
				return "Match '".$arg."' in (http://tikiwiki.org/".$page['data'][0]['pageName'].") (#".$args[0].")";
			} else {
				return "Sorry, no page name matches ".$arg;
			}
		}
	}

	function tiki_help($arg,$args) {
		$help = "tiki_$arg";
		if (method_exists($this,$help)) {
			return $this->$help('help',1);
		} else {
			return "[!T help] who stats rpage art dir find";
		}
	}

}

function tra($s) { return $s; }

$wollabot->register_module("tiki");

?>
