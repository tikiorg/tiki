<?php # $CVSHeader$

# Copyright (c) 2002-2003, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
# All Rights Reserved. See copyright.txt for details and a complete list of authors.
# Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

error_reporting(E_ALL);
set_time_limit(60);

require_once('irclib.php');

$channel = isset($argv[1]) ? $argv[1] : 'tikiwiki';

# \TODO get domain from mySQL

$options = array(
    'server'    => 'sterling.freenode.net',
    'port'      => 6667,
    'nick'      => 'tikibot',
    'realname'  => 'http://wiki.netebb.com/irc',
    'identd'    => 'tikibot',
    'host'      => '127.0.0.1',
    'log_types' => array(0, 1),
	'channel'	=> $channel,
);

$irc =& new IRC_Logger();
$irc->debug(TRUE);
if (!$irc->connect($options)) {
    die ('Could not connect to ' . $options['server']);
}

$irc->loopRead();

?>
