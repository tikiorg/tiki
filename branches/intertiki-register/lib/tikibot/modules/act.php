<?php /* 
$Id: /cvsroot/tikiwiki/tiki/lib/tikibot/modules/act.php,v 1.2 2003-11-18 01:34:56 mose Exp $

Act module for wollabot 

*/

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
