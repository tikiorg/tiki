<?php # $CVSHeader$

# Copyright (c) 2002-2003, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
# All Rights Reserved. See copyright.txt for details and a complete list of authors.
# Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

require_once dirname(dirname(__FILE__)) . '/pear/IRC.php';

defined('IRC_LOG_DIR') ||
 define('IRC_LOG_DIR', dirname(dirname(dirname(__FILE__))) . '/var/log/irc');

class IRC_Logger extends Net_IRC_Event {
	var $_logname;
	var $nick;
	var $channel;
	var $_debug = false;
	var $joined = false;
	
	function debug($debug = null) {
		if (!is_null($debug)) {
			$this->_debug = $debug;
		}

		if ($debug) {
			$this->options['log_types'] = array(0, 1, 2, 3, 4, 5, 6);
		}

		return $this->_debug;
	}
	
	function connect($options) {
		$this->nick		= isset($this->options['nick']) ? $this->options['nick'] : false;
		$this->channel	= isset($this->options['channel']) ? $this->options['channel'] : false;

		$rv = parent::connect($options);
		
		if (!$rv) {
			return false;
		}

		$this->start();

		if ($this->channel) {
			$this->command('JOIN #' . $this->channel);
		}

		$this->joined = true;
		
		return true;
	}

	function start() {
		$this->_log(sprintf('Session Start (%s:#%s): %s', 
			$this->options['server'], $this->options['channel'], date('r')));
	}
	
	function finish() {
		$this->_log(sprintf('Session Close (#%s): %s', 
			$this->options['channel'], date('r')));
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

	function GMTOffset() {
		$o = date('Z') / 60;
		$hh = intval(abs($o) / 60);
		$mm = intval(abs($o) - $hh * 60);
		$sign = $o < 0 ? '-' : '+';

		return sprintf('%s%02d%02d', $sign, $hh, $mm);
	}
	
	function _log($s) {
		$this->logname(IRC_LOG_DIR . '/' . $this->channel . '_' . date('ymd') . '.log');
		$fp = @fopen($this->_logname, 'ab');
		if (!$fp) {
			printf("Can't open '%s': %s\n", $this->_logname, $php_errormsg);
			return false;
		}
		$s = sprintf("[%s %s] %s\n", date('m-d-y/H:i:s'), $this->GMTOffset(), $s);
		if ($this->_debug) {
			print $s;
		}
			
		@fwrite($fp, $s);
		@fclose($fp);
		@chmod($this->_logname, 0664);
		return true;
	}

    function event_action($nick, $nickhost, $channel, $params) {
    	if ($nick == $this->nick) {
    		return;
    	}
    	$this->_log('* ' . $nick . ' ' . $params);
    }

    function event_join($nick, $nickhost, $channel, $params) {
    	if ($nick == $this->nick) {
    		return;
    	}
    	$this->_log('*** Joined ' . $nick . ' (' . $nickhost . ')');
    }

    function event_nick($nick, $nickhost, $channel, $params) {
    	if ($nick == $this->nick) {
    		return;
    	}
    	$this->_log('*** ' . $nick . ' is now known as ' . $channel);
    }

    function event_notice($nick, $nickhost, $channel, $params) {
    	if ($nick == $this->nick) {
    		return;
    	}
    	$this->_log('*** ' . $nick . ': ' . $channel . ' ' . $params);
    }

    function event_part($nick, $nickhost, $channel, $params) {
    	if ($nick == $this->nick) {
    		return;
    	}
    	$this->_log('*** Parted ' . $nick . ' (' . $nickhost . ')');
    }

    function event_privmsg($nick, $nickhost, $channel, $params) {
    	if ($nick == $this->nick) {
    		return;
    	}

		$params = str_replace("\001", '', $params);
		$params = trim($params);
		if (substr($params, 0, 6) == 'ACTION') {
			$params = substr($params, 7);
	    	$this->_log('* ' . $nick . ' ' . $params);
	    	return;
		}
    	$this->_log('<' . $nick . '> ' . $params);
    }
    
    function event_topic($nick, $nickhost, $channel, $params) {
    	if ($nick == $this->nick) {
    		return;
    	}
    	$this->_log('*** ' . $nick . ' has changed to topic to "' . $params . '"');
    }

    function fallback($origin, $orighost, $target, $params) {
    	$rv = parent::fallback($origin, $orighost, $target, $params);
		if ($this->joined && $this->_debug) {
echo "
origin  ='$origin'
orighost='$orighost'
target  ='$target'
params  ='$params'
";
		}
		return $rv;
    }
}

/**
 * \static
 */
class IRC_Log_Parser {
	/**
	 * \static
	 */
	function parse($lines, $date_filter = null) {
		$rows		= array();
		$name_hash	= array();

		foreach($lines as $line) {
			$original	= $line;
			$action		= 'n';
			$date		= '';
			$localtime	= '';
			$name		= '';
			$nick		= '';
			$offset		= IRC_Logger::GMTOffset();
			$time		= '';

			$line		= trim($line);
			$pre_date	= '';
			if (preg_match('/^\s*\[([^\]]*)\](.*)/', $line, $m)) {
				$pre_date	= trim($m[1]);
				$line		= trim($m[2]);
			}

			if (!$pre_date && preg_match('/^\s*(\S+)(.*)/', $line, $m)) {
				$pre_date	= trim($m[1]);
				$line		= trim($m[2]);
			}

			if ($pre_date && preg_match('/^\s*([^\/]+)\/(\S+)\s+([+\-]?\d{4})/', $pre_date, $m)) {
				$date	= trim($m[1]);
				$time	= trim($m[2]);
				$offset	= trim($m[3]);
			}

			if (!$date && preg_match('/^\s*([^\/]+)\/(\S+)/', $pre_date, $m)) {
				$date = trim($m[1]);
				$time = trim($m[2]);
			}

			if (!$date && preg_match('/^\s*(\S+)\s+(.*)/', $pre_date, $m)) {
				$date = trim($m[1]);
				$time = trim($m[2]);
			}

			if ($time) {
				if (preg_match('/^(\d\d):(\d\d)/', $time, $m)) {
					$hh = $m[1];
					$hhnn = $m[1] . $m[2];
					if (!isset($name_hash[$hh])) {
						$name = '<a name="' . $hh . '">';
						$name_hash[$hh] = 1;
					}

					if (!isset($name_hash[$hhnn])) {
						$name .= "\n<a name=\"" . $hhnn . '">';
						$name_hash[$hhnn] = 1;
					}
				}
			}

			if (preg_match('/^\*\*\*\s+(.*)/', $line, $m)) {
				$action = 'v';
				$line	= trim($m[1]);
			}

			if (preg_match('/^\*\s+(.*)/', $line, $m)) {
				$action = 'a';
				$line	= trim($m[1]);
			}

			if (preg_match('/^<([^>]*)>(.*)/', $line, $m)) {
				$nick = trim($m[1]);
				$line = trim($m[2]);
			}

			if (!$line)
				continue;

			if (preg_match('/(.*)((https?|ftp):\/\/[-_\.a-zA-Z0-9\/\?\&=%]*)(.*)/', $line, $m)) {
				$line = htmlspecialchars($m[1], ENT_NOQUOTES) .
					'<a href="' . $m[2] . '">' . $m[2] . '</a>' .
					htmlspecialchars($m[4], ENT_NOQUOTES);
			} else {
				$line = htmlspecialchars($line, ENT_NOQUOTES);
			}

			$rows[] = array(
				'action'	=> $action,
				'data'		=> $line,
				'name'		=> $name,
				'nick'		=> $nick,
				'date'		=> $date,
				'time'		=> $time,
				'localtime'	=> $localtime,
				'original'	=> $original,
			);
		}

		return $rows;
	}

	/**
	 * \static
	 */
	function getChannelAndDate($file) {
		$channel	= false;
		$date		= false;

		$file = basename($file);
		
		if (preg_match('/^([^\.]*)_(\d{2})(\d{2})(\d{2})/', $file, $m)) {
			$channel	= $m[1];
			$date		= mktime(12, 0, 0, $m[3], $m[4], $m[2]);
		}

		if (!$channel && preg_match('/^#?(\w+)/', $file, $m)) {
			$channel = $m[1];
		}

		return array(
			'channel'	=> $channel,
			'date'		=> $date,
		);

	}

	/**
	 * \static
	 */
	function parseFile($file, $date_filter = null) {
		$lines = file($file);
		if (!$lines) {
			return false;
		}
		return IRC_Log_Parser::parse($lines);
	}

	/**
	 * \static
	 */
	function splitFile($file) {
		$rows = IRC_Log_Parser::parseFile($file, null);

		$a = IRC_Log_Parser::getChannelAndDate($file);

		if (!$a['channel']) {
			return false;
		}

		$channel = $a['channel'];
		$file = '';
		$fp = null;

		foreach($rows as $row) {
			$date = $row['date'];
			if (!$date) {
				continue;
			}

			if (preg_match('/(\d{1,2})[\/\-]?(\d{1,2})[\/\-]?(\d{2,4})/', $date, $m)) {
				$newfile = sprintf('%s_%02d%02d%02d.log', $channel, $m[3], $m[1], $m[2]);
				if ($newfile <> $file) {
					if ($fp) {
						fclose($fp);
						$fp = null;
					}
					$file = $newfile;
				}
			}
		
			if ($file) {
				if (!$fp) {
					$fullname = IRC_LOG_DIR . '/' . $file;
					$fp = fopen($fullname, 'wb');
					chmod($fullname, 0664);
					if (!$fp) {
						printf("Can't open '%s': %s\n", $fullname, $php_errormsg);
						return false;
					}
					printf("Creating '%s'\n", $fullname);
				}
			}
		
			fwrite($fp, $row['original']);
		}

		if ($fp) {
			fclose($fp);
			$fp = null;
		}

		return true;		
	}

	/**
	 * \static
	 */
	function splitFiles($f) {
		if (is_dir($f)) {
			$dh = opendir($f);
			while ($f2 = readdir($dh)) {
				if ($f2 == '.' || $f2 == '..') {
					continue;
				}
				if (is_dir($f2)) {
					IRC_Log_Parser::splitFiles($f . '/' . $f2);
					continue;
				}
			}
			closedir($dh);
			return true;
		}
		if (!is_readable($f)) {
			printf("Can't read '%s'\n", $f);
			return true;
		}
		printf("Reading '%s'\n", $f);
		return IRC_Log_Parser::splitFile($f);
	}

	/**
	 * \static
	 */
	function getDates($file) {
		$rows = IRC_Log_Parser::parseFile($file, null);

		$start	= null;
		$end	= null;

		foreach($rows as $row) {
			$date = $row['date'];
			if (!$date) {
				continue;
			}

			if (preg_match('/(\d{1,2})[\/\-]?(\d{1,2})[\/\-]?(\d{2,4})/', $date, $m)) {
				$time	= mktime(12, 0, 0, $m[2], $m[3], $m[1]);
				
				if (!$start || $time < $start) {
					$start = $time;
				}

				if (!$end || $time > $end) {
					$end = $time;
				}
				
			}
		}
		
		return array(
			'start'	=> $start,
			'end'	=> $end,
		);
	}
}

?>
