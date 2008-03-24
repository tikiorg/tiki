<?php /* 
$Header: /cvsroot/tikiwiki/tiki/lib/tikibot/modules/nickometer.php,v 1.3 2004-05-12 13:15:32 damosoft Exp $

Lame nickometer for wollabot 
*/

class nickometer extends Wollabot_Module {

	var $score = 0;

	function nickometer() {
		$this->bind_prefix('nicko','!nickometer');
	}

	function slow_pow ($x, $y) {
		return pow($x, $this->slow_exponent($y));
	}

	function slow_exponent ($x) {
		return 1.3 * $x * (1 - atan($x/6) *2/pi());
	}

	function punish ($x, $y) {
		global $score;
		$score += $x;
	}

	function nicko($params) {
		global $score, $conf;
		
		if (substr($params['channel'],0,1) == '#') {
			$target = $params['channel'];
			$who = $params["nick"].": ";
		} else {
			$target = $params["nick"];
			$who = '';
		}
		$nick = $params["message_exploded"][1];
		
		$score = $shifts = 0;

		$this->wollabot->print_log("NICKOMETER: Testing '$nick' for '$target'");

		$special_cost = array(
		'gaim'			=> 300,
		'69'			=> 500,
		'dea?th'		=> 500,
		'dark'			=> 400,
		'n[i1]ght'		=> 300,
		'n[i1]te'		=> 500,
		'fuck'			=> 500,
		'sh[i1]t'		=> 500,
		'coo[l1]'		=> 500,
		'cheal'		=> 500,
		'healer'		=> 500,
		'kew[l1]'		=> 500,
		'lame'			=> 500,
		'dood'			=> 500,
		'dude'			=> 500,
		'[l1](oo?|u)[sz]er'	=> 500,
		'[l1]eet'		=> 500,
		'e[l1]ite'		=> 500,
		'[l1]ord'		=> 500,
		'pron'			=> 1000,
		'warez'			=> 1000,
		'xx'			=> 100,
		'\[rkx]0'		=> 1000,
		'\0[rkx]'		=> 1000
		);

		foreach ($special_cost as $special => $cost) {
			$special_pattern = $special;
			if (eregi($special, $nick)) $this->punish($cost, "special");
		}
		
		$clean = eregi_replace("[^A-Z0-9]", "", $nick);
		$this->punish(pow(10, (strlen($nick) - strlen($clean)))-1, "non-alha ($clean vs $nick)");
		
		$k3wlt0k_weights = array(5, 5, 2, 5, 2, 3, 1, 2, 2, 2);
		for ($digit = 0; $digit < 10; $digit++) {
			$this->punish($k3wlt0k_weights[$digit] * substr_count($nick, $digit), "leet digits");
		}
		
		$this->punish($this->slow_pow(9, similar_text($nick, strtoupper($nick)))-1, "lowercase");
				 
		if (ereg("^.*[XZ]$", $nick)) $this->punish(50, "lame endings");

		if (eregi("[0-9][a-z]", $nick, $regs)) {
			$shifts = @sizeof($regs) - 1;
			unset($regs);
		}

		if (eregi("[a-z][0-9]", $nick, $regs)) {
			$shifts = @sizeof($regs) - 1;
			unset($regs);
		}

		$this->punish($this->slow_pow(9, $shifts) - 1, "shifts");

		if (ereg("[A-Z]", $nick, $regs)) {
			$caps = @sizeof($regs) - 1;
			unset($regs);
			$this->punish($this->slow_pow(7, $caps), "upper case");
		}

		$percentage = 100 * (1 + tanh(($score-400)/400)) * (1 - 1/(1+$score/5)) / 2;
		$digits = 2 * (2 - floor(log(100 - $percentage) / log(10)));

		$this->send_privmsg($target, "'$nick' is ".sprintf ("%.".$digits."f", $percentage)."% lame");
	}
}

$wollabot->register_module("nickometer");

?>
