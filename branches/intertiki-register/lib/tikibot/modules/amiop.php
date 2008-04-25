<?php /* 
$Id: /cvsroot/tikiwiki/tiki/lib/tikibot/modules/amiop.php,v 1.2 2003-11-18 01:34:56 mose Exp $

test comand to check if you are op
*/

class amiop extends Wollabot_Module {
	function amiop() {
		$this->bind_prefix('ami','!amiop');
	}

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
