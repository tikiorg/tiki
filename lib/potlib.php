<?php

// potlib.php - phpOpenTracker implementation for Tiki
// 
// by LeChuckDaPirate

require_once("phpOpenTracker.php");

function encode_exit_urls($buffer) {  
	return preg_replace("#<a href=(\"|')http://([^\"']+)(\"|')#ime",
	                    '"<a href=\"exit.php?url=".base64_encode(\'\\2\')."\""'
	                    ,$buffer);
}
	                    

class potlib extends TikiLib {
	
	var $id = 1;  				// OpenTracker Id for future multi-portal feature
	var $log_outgoing = true; 	// Wether or not log outgoing links...
	
	function potlib($a_id = 1, $outgoing = true;) {
		$this->id = $a_id;
		$this->log_outgoing = $outgoing;
		if ($this->log_outgoing) { ob_start('encode_exit_urls'); }
	}
	
}

?>