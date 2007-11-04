<?php

// $Header: /cvsroot/tikiwiki/tiki/lib/setup/tikisetup.class.php,v 1.1.2.1 2007-11-04 22:08:34 nyloth Exp $
// Copyright (c) 2002-2007, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for
// details.

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER['SCRIPT_NAME'],'tiki-setup.php')!=FALSE) {
  header('location: index.php');
  exit;
}

class TikiSetup extends TikiInit {

    /*!
        Check that everything is set up properly

        \static
    */
    function check($tikidomain='') {
        static $checked;

        if ($checked) {
            return;
        }

        $checked = true;

        $errors = '';

        if (strpos($_SERVER['SERVER_SOFTWARE'],'IIS')==TRUE){
		if (array_key_exists('PATH_TRANSLATED', $_SERVER)) {
        	$docroot = dirname($_SERVER['PATH_TRANSLATED']);
		} else {
			$docroot = getcwd();
		}
        }
        else{
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
                } else if (!is_writeable($save_path)) {
                    $errors .= "The directory '$save_path' is not writeable.\n";
                }
	    }

            if ($errors) {
                $save_path = TikiSetup::tempdir();

                if (is_dir($save_path) && is_writeable($save_path)) {
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
            'backups',
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
            } else if (!is_writeable("$docroot/$dir/$tikidomain")) {
                $errors .= "The directory '$docroot/$dir/$tikidomain' is not writeable by $wwwuser.\n";
            }
        }

        if ($errors) {
            $PHP_CONFIG_FILE_PATH = PHP_CONFIG_FILE_PATH;

            ob_start();
            phpinfo (INFO_MODULES);
            $httpd_conf = 'httpd.conf';

            if (preg_match('/Server Root<\/b><\/td><td\s+align="left">([^<]*)</', ob_get_contents(), $m)) {
                $httpd_conf = $m[1] . '/' . $httpd_conf;
            }

            ob_end_clean();

            print "
<html><body>
<h2><font color='red'>Tikiwiki is not properly set up:</font></h1>
<pre>
$errors
";
						if ($tikidomain) {
							$install_link = '?multi='.urlencode($tikidomain);
						}
            if (!TikiSetup::isWindows()) {
                print "You may either chmod the directories above manually to 777, or run one of the sets of commands below.
<b><a href='tiki-install.php$install_link'>Proceed to the Tiki installer</a></b> after you run the commands below.

If you cannot become root, and are NOT part of the group $wwwgroup:
    \$ bash
    \$ cd $docroot
    \$ chmod +x setup.sh
    \$ ./setup.sh yourlogin yourgroup 02777
    Tip: You can find your group using the command 'id'.

If you cannot become root, but are a member of the group $wwwgroup:
    \$ bash
    \$ cd $docroot
    \$ chmod +x setup.sh
    \$ ./setup.sh mylogin $wwwgroup</i>

If you can become root:
    \$ bash
    \$ cd $docroot
    \$ chmod +x setup.sh
    \$ su -c './setup.sh $wwwuser'

If you have problems accessing a directory, check the open_basedir entry in
$PHP_CONFIG_FILE_PATH/php.ini or $httpd_conf.

Once you have executed these commands, this message will disappear!

Note: If you cannot become root, you will not be able to delete certain
files created by apache, and will need to ask your system administrator
to delete them for you if needed.

<a href='http://tikiwiki.org/InstallTiki' target='_blank'>Consult the tikiwiki.org installation guide</a> if you need more help.

<b><a href='tiki-install.php'>Proceed to the Tiki installer</a></b> if you've completed the steps above.
</pre></body></html>";
            }

            exit;
        }


    }
}
