<?php # $CVSHeader$

# Copyright (c) 2002-2003, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
# All Rights Reserved. See copyright.txt for details and a complete list of authors.
# Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

require_once dirname(dirname(__FILE__)) . '/pear/IRC.php';

defined('TIKI_IRCBOT_LOG_DIR') ||
 define('TIKI_IRCBOT_LOG_DIR', dirname(dirname(dirname(__FILE__))) . '/var/log/ircbot');

define('TIKI_IRCBOT_ACTION',	'Action');
define('TIKI_IRCBOT_JOINED',	'Joined');
define('TIKI_IRCBOT_LOGGING',	'Log');
define('TIKI_IRCBOT_NICK',		'Nick');
define('TIKI_IRCBOT_PART',		'Part');
define('TIKI_IRCBOT_PRIVMSG',	'Privmsg');
define('TIKI_IRCBOT_TOPIC',		'Topic'	);

class IRCbot extends Net_IRC_Event {
	var $_logname;
	var $nick;
	var $channel;
	
	function connect($options) {
		$rv = parent::connect($options);
		
		if (!$rv)
			return false;

		$this->nick		= $this->options['nick'];
		$this->channel	= isset($this->options['channel']) ? $this->options['channel'] : false;

		$this->logname(TIKI_IRCBOT_LOG_DIR . '/' . date('ymd') . '_' . $this->channel . '.log');
		$this->_log('Log: started');

		if ($this->channel) {
			$this->command('JOIN #' . $this->channel);
		}
		
		return true;
	}
	
	function logname($logname) {
		$this->_logname = $logname;
		$dir = dirname($this->_logname);
		if (!is_dir($dir)) {
			@mkdir($dir, 0664);
		}
		if (!is_dir($dir)) {
			die (sprintf("Can't create directory '%s': %s\n", $dir, $php_errormsg));
		}
	}

	function _log($s) {
		$fp = @fopen($this->_logname, 'ab');
		if (!$fp) {
			printf("Can't open '%s': %s\n", $this->_logname, $php_errormsg);
			return false;
		}
		$s = sprintf("[%s] %s\n", strftime('%T %Z'), $s);
		print $s;
		fwrite($fp, $s);
		fclose($fp);
		@chmod($this->_logname, 0664);
		return true;
	}
	# $nick, $nickhost, $channel, $topic

    function event_action($nick, $nickhost, $channel, $params) {
    	if ($nick == $this->nick)
    		return;
    	$this->_log('Action: <' . $nick . '> ' . $params);
    }

    function event_join($nick, $nickhost, $channel, $params) {
    	if ($nick == $this->nick)
    		return;
    	$this->_log('Joined: ' . $nick . ' (' . $nickhost . ')');
    }

    function event_nick($nick, $nickhost, $channel, $params) {
    	if ($nick == $this->nick)
    		return;
    	$this->_log('Nick: ' . $nick . ' is now known as ' . $channel);
    }

    function event_part($nick, $nickhost, $channel, $params) {
    	if ($nick == $this->nick)
    		return;
    	$this->_log('Part: ' . $nick . ' ' . $params);
    }

    function event_privmsg($nick, $nickhost, $channel, $params) {
    	if ($nick == $this->nick)
    		return;

		$params = str_replace("\001", '', $params);
		$params = trim($params);
		if (substr($params, 0, 6) == 'ACTION') {
			$params = substr($params, 7);
	    	$this->_log('Action: <' . $nick . '> ' . $params);
	    	return;
		}
    	$this->_log('<' . $nick . '> ' . $params);
    }
    
    function event_topic($nick, $nickhost, $channel, $params) {
    	if ($nick == $this->nick)
    		return;
    	$this->_log('Topic: ' . $nick . ' has changed to topic to "' . $params . '"');
    }
}

?>
