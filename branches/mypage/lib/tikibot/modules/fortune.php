<?php /* 
$Id: /cvsroot/tikiwiki/tiki/lib/tikibot/modules/fortune.php,v 1.2 2003-11-18 01:34:56 mose Exp $

fortune-script for Wollabot 
*/

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
