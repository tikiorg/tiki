<?php

// potlib.php - phpOpenTracker implementation for Tiki
// 
// by LeChuckDaPirate

	require_once("phpOpenTracker.php");

// -----------------------------------------

function encode_exit_urls($buffer) {  
	return preg_replace("#<a href=(\"|')http://([^\"']+)(\"|')#ime",
	                    '"<a href=\"exit.php?url=".base64_encode(\'\\2\')."\""'
	                    ,$buffer);
}
	                    
class tiki_phpOpenTracker extends TikiLib, phpOpenTracker {

	
	var $id = 1;  				// OpenTracker Id for future multi-portal feature
	var $log_outgoing = true; 	// Wether or not log outgoing links...
	
	function potlib($a_id = 1, $outgoing = true;) {
		$this->id = $a_id;
		$this->log_outgoing = $outgoing;
		if ($this->log_outgoing) { ob_start('encode_exit_urls'); }
	}
	
	function singleton() {
		phpOpenTracker::singleton();
		$this->config['db_host']				 = $host_tiki;
		$this->config['db_user'] 				 = $user_tiki;
		$this->config['db_password'] 			 = $pass_tiki;
		$this->config['additional_data_table']   = "pot_add_data"
		$this->config['accesslog_table']         = "pot_accesslog"
		$this->config['documents_table']         = "pot_documents"
		$this->config['exit_targets_table']      = "exit_targets"
		$this->config['hostnames_table']         = "pot_hostnames"
		$this->config['operating_systems_table'] = "pot_operating_systems"
		$this->config['referers_table']          = "pot_referers"
		$this->config['user_agents_table']       = "pot_user_agents"
		$this->config['visitors_table']          = "pot_visitors"
	}
	
	function db_is_created() {
		$res = $this->query("select tables like 'pot_%'");
		if (!$result->numrows()) {
			$handle = @fopen("phpOpenTracker/docs
		}
	}
		
}

?>