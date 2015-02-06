<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

require_once('lib/init/initlib.php');

class TikiSetup extends TikiInit
{
	/*!
		Check that everything is set up properly

		\static
	*/
	static function check($tikidomain = '')
	{
		static $checked;

		if ($checked) {
			return;
		}

		$checked = true;

		$errors = '';

		if (strpos($_SERVER['SERVER_SOFTWARE'], 'IIS') == TRUE) {
			if (array_key_exists('SCRIPT_FILENAME', $_SERVER)) {
				$docroot = dirname($_SERVER['SCRIPT_FILENAME']);
			} elseif (array_key_exists('PATH_TRANSLATED', $_SERVER)) {
				$docroot = dirname($_SERVER['PATH_TRANSLATED']);
			} else {
				$docroot = getcwd();
			}
		} else {
			$docroot = getcwd();
		}

		if (ini_get('session.save_handler') == 'files') {
			$save_path = ini_get('session.save_path');
			// check if we can check it. The session.save_path can be outside
			// the open_basedir paths.
			$open_basedir=ini_get('open_basedir');
			if (empty($open_basedir)) {
				if (!is_dir($save_path)) {
					$errors .= "The directory '$save_path' does not exist or PHP is not allowed to access it (check open_basedir entry in php.ini).\n";
				} else if (!TikiSetup::is_writeable($save_path)) {
					$errors .= "The directory '$save_path' is not writeable.\n";
				}
			}

			if ($errors) {
				$save_path = TikiSetup::tempdir();

				if (is_dir($save_path) && TikiSetup::is_writeable($save_path)) {
					session_save_path($save_path);
					$errors = '';
				}
			}
		}

		$wwwuser = '';
		$wwwgroup = '';

		if (TikiSetup::isWindows()) {
			$wwwuser = 'SYSTEM';
			$wwwgroup = 'SYSTEM';
		}

		if (function_exists('posix_getuid')) {
			$user = @posix_getpwuid(@posix_getuid());

			$group = @posix_getpwuid(@posix_getgid());
			$wwwuser = $user ? $user['name'] : false;
			$wwwgroup = $group ? $group['name'] : false;
		}

		if (!$wwwuser) {
			$wwwuser = 'nobody (or the user account the web server is running under)';
		}

		if (!$wwwgroup) {
			$wwwgroup = 'nobody (or the group account the web server is running under)';
		}

		static $dirs = array(
				'dump',
				'img/wiki',
				'img/wiki_up',
				'modules/cache',
				'temp',
				'templates_c',
				# 'var',
				# 'var/log',
				# 'var/log/irc',
		);

		foreach ($dirs as $dir) {
			if (!is_dir("$docroot/$dir/$tikidomain")) {
				$errors .= "The directory '$docroot/$dir/$tikidomain' does not exist.\n";
			} else {
				if (!TikiSetup::is_writeable("$docroot/$dir/$tikidomain")) {
					$errors .= "The directory '$docroot/$dir/$tikidomain' is not writeable by $wwwuser.\n";
				}
			}
		}

		if ($errors) {
			$PHP_CONFIG_FILE_PATH = PHP_CONFIG_FILE_PATH;

			ob_start();
			phpinfo(INFO_MODULES);
			$httpd_conf = 'httpd.conf';

			if (preg_match('/Server Root<\/b><\/td><td\s+align="left">([^<]*)</', ob_get_contents(), $m)) {
				$httpd_conf = $m[1] . '/' . $httpd_conf;
			}

			ob_end_clean();

			print "
<html><body>
<h2><font color='red'>Tiki is not properly set up:</font></h2>
<pre>
$errors
";
			if ($tikidomain) {
				$install_link = '?multi='.urlencode($tikidomain);
			}

			if (!TikiSetup::isWindows()) {
				print "Your options:


	1- With FTP access:
		a) Change the permissions (chmod) of the directories to 777.
		b) Create any missing directories
		c) <a href='tiki-install.php$install_link'>Execute the Tiki installer again</a> (Once you have executed these commands, this message will disappear!)

	or

	2- With shell (SSH) access, you can run the command below.

		a) Run setup.sh and follow the instructions:
			\$ bash
			\$ cd $docroot
			\$ sh setup.sh

		The script will offer you options depending on your server configuration.

		b) <a href='tiki-install.php$install_link'>Execute the Tiki installer again</a> (Once you have executed these commands, this message will disappear!)


<hr>
If you have problems accessing a directory, check the open_basedir entry in
$PHP_CONFIG_FILE_PATH/php.ini or $httpd_conf.

<hr>

<a href='http://doc.tiki.org/Installation' target='_blank'>Consult the tiki.org installation guide</a> if you need more help or <a href='http://tiki.org/tiki-forums.php' target='_blank'>visit the forums</a>

</pre></body></html>";
			}
			exit;
		}
	}
}
