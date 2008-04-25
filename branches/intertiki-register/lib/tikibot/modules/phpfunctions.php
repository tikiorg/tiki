<?php /* 
$Id: /cvsroot/tikiwiki/tiki/lib/tikibot/modules/phpfunctions.php,v 1.2 2003-11-18 01:34:56 mose Exp $

PHP function module for Wollabot 
*/

class phpfunctions extends Wollabot_Module {
	
	function phpfunctions() {
		$this->bind_prefix('php_lookup','!php ');
	}

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
