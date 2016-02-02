<?php
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\Dumper\PhpDumper;

/**
 * Tiki initialization functions and classes
 *
 * @package TikiWiki
 * @subpackage lib\init
 * @copyright (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project. All Rights Reserved. See copyright.txt for details and a complete list of authors.
 * @licence Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
 */
// $Id$

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER['SCRIPT_NAME'], basename(__FILE__)) !== false) {
  header('location: index.php');
  exit;
}

if (! file_exists(__DIR__ . '/../../vendor/autoload.php')) {
	echo "Your Tiki is not completely installed because Composer has not been run to fetch package dependencies.\n";
	echo "You need to run 'sh setup.sh' from the command line.\n";
	echo "See https://dev.tiki.org/Composer for details.\n";
	exit;
}

require_once __DIR__ . '/../../vendor/autoload.php';

/**
 * performs some checks on the underlying system, before initializing Tiki.
 * @package TikiWiki\lib\init
 */
class TikiInit
{
	/**
	 * dummy constructor
	 */
	function __construct()
	{
	}

	static function getContainer()
	{
		static $container;

		if ($container) {
			return $container;
		}

		$cache = TIKI_PATH . '/temp/cache/container.php';
		if (is_readable($cache)) {
			require_once $cache;
			$container = new TikiCachedContainer;

			/* If the server moved, the container must be recreated */
			if (TIKI_PATH == $container->getParameter('kernel.root_dir')) {
				if (TikiDb::get()) {
					$container->set('tiki.lib.db', TikiDb::get());
				}
				return $container;
			} else {
				/* This server moved, container must be recreated */
				unlink($cache);
			}

		}

		$path = TIKI_PATH . '/db/config';
		$container = new ContainerBuilder;
		$container->addCompilerPass(new \Tiki\MailIn\Provider\CompilerPass);
		$container->addCompilerPass(new \Tiki\Recommendation\Engine\CompilerPass);
		$container->addCompilerPass(new \Tiki\Wiki\SlugManager\CompilerPass);
		$container->addCompilerPass(new \Search\Federated\CompilerPass);
		$container->addCompilerPass(new \Tracker\CompilerPass);

		$container->setParameter('kernel.root_dir', TIKI_PATH);
		$loader = new XmlFileLoader($container, new FileLocator($path));

		$loader->load('tiki.xml');
		$loader->load('controllers.xml');
		$loader->load('mailin.xml');

		try {
			$loader->load('custom.xml');
		} catch (InvalidArgumentException $e) {
			// Do nothing, absence of custom.xml file is expected
		}

		foreach ( glob( TIKI_PATH . '/addons/*/lib/libs.xml' ) as $file ) {
			try {
				$loader->load($file);
			} catch (InvalidArgumentException $e) {
				// Do nothing, absence of libs.xml file is expected
			}
		}

		if (TikiDb::get()) {
			$container->set('tiki.lib.db', TikiDb::get());
		}

		$container->compile();

		$dumper = new PhpDumper($container);
		file_put_contents($cache, $dumper->dump([
			'class' => 'TikiCachedContainer',
		]));

		return $container;
	}

/** Return 'windows' if windows, otherwise 'unix'
 * \static
 */
	function os()
	{
		static $os;
		if (!isset($os)) {
			if (strtoupper(substr(PHP_OS, 0, 3)) == 'WIN') {
				$os = 'windows';
			} else {
				$os = 'unix';
			}
		}
		return $os;
	}


/** Return true if windows, otherwise false
  * @static
  */
	static function isWindows()
	{
		static $windows;
		if (!isset($windows)) {
			$windows = strtoupper(substr(PHP_OS, 0, 3)) == 'WIN';
		}
		return $windows;
	}

	/**
	 * Copes with Windows permissions
	 *
	 * @param string $path directory to test
	 *
	 * @return bool
	 */
	static function is_writeable($path)
	{
		if (self::isWindows()) {
			return self::is__writable($path);
		} else {
			return is_writeable($path);
		}
	}

	/**
	 * From the php is_writable manual (thanks legolas558 d0t users dot sf dot net)
	 * Note the two underscores and no "e".
	 * 
	 * will work in despite of Windows ACLs bug
	 * NOTE: use a trailing slash for folders!!!
	 * {@see http://bugs.php.net/bug.php?id=27609}
	 * {@see http://bugs.php.net/bug.php?id=30931}
	 * 
	 * @param string $path	directory to test	NOTE: use a trailing slash for folders!!!
	 * @return bool
	 */
	static function is__writable($path)
	{
		if ($path{strlen($path)-1}=='/') { // recursively return a temporary file path
			return self::is__writable($path.uniqid(mt_rand()).'.tmp');
		} else if (is_dir($path)) {
			return self::is__writable($path.'/'.uniqid(mt_rand()).'.tmp');
		}
		// check tmp file for read/write capabilities
		$rm = file_exists($path);
		$f = @fopen($path, 'a');
		if ($f===false)
			return false;
		fclose($f);
		if (!$rm)
			unlink($path);
		return true;
	}


    /** Prepend $path to the include path
     * @static          
     * @param string $path the path to prepend
     * @return string
     */
	static function prependIncludePath($path)
	{
		$include_path = ini_get('include_path');
		$paths = explode(PATH_SEPARATOR, $include_path);

		if ($include_path && !in_array($path, $paths)) {
			$include_path = $path . PATH_SEPARATOR . $include_path;
		} else if (!$include_path) {
			$include_path = $path;
		}

		return set_include_path($include_path);
	}


    /** Append $path to the include path
     * @static 
     * @param mixed $path
     */
	static function appendIncludePath($path)
	{
		$include_path = ini_get('include_path');
		$paths = explode(PATH_SEPARATOR, $include_path);

		if ($include_path && !in_array($path, $paths)) {
			$include_path .= PATH_SEPARATOR . $path;
		} else if (!$include_path) {
			$include_path = $path;
		}

		return set_include_path($include_path);
	}


    /** Return system defined temporary directory.
     * In Unix, this is usually /tmp
     * In Windows, this is usually c:\windows\temp or c:\winnt\temp
     * @static
     */
	static function tempdir()
	{
		static $tempdir;
		if (!$tempdir) {
			$tempfile = @tempnam(false, '');
			$tempdir = dirname($tempfile);
			@unlink($tempfile);
		}
		return $tempdir;
	}

	/**
	 * Convert a string to UTF-8. Fixes a bug in PHP decode
	 * From http://w3.org/International/questions/qa-forms-utf-8.html
	 * @static
	 * @param string String to be converted
	 * @return UTF-8 representation of the string
	 */
	static function to_utf8( $string )
	{
		if ( preg_match(
			'%^(?:
	  		   [\x09\x0A\x0D\x20-\x7E]            # ASCII
   		 | [\xC2-\xDF][\x80-\xBF]             # non-overlong 2-byte
		    | \xE0[\xA0-\xBF][\x80-\xBF]         # excluding overlongs
   		 | [\xE1-\xEC\xEE\xEF][\x80-\xBF]{2}  # straight 3-byte
		    | \xED[\x80-\x9F][\x80-\xBF]         # excluding surrogates
			 | \xF0[\x90-\xBF][\x80-\xBF]{2}      # planes 1-3
			 | [\xF1-\xF3][\x80-\xBF]{3}          # planes 4-15
		    | \xF4[\x80-\x8F][\x80-\xBF]{2}      # plane 16
			)*$%xs',
			$string
		)
		) {
			return $string;
		} else {
			return iconv('CP1252', 'UTF-8', $string);
		}
	}

	/**
	 * Determine if the web server is an IIS server
	 * @return true if IIS server, else false
  	 * @static
	 */
	static function isIIS()
	{
		static $IIS;

		// Sample value Microsoft-IIS/7.5
		if (!isset($IIS) && isset($_SERVER['SERVER_SOFTWARE'])) {
			$IIS = substr($_SERVER['SERVER_SOFTWARE'], 0, 13) == 'Microsoft-IIS';
		}

		return $IIS;
	}

	/**
	 * Determine if the web server is an IIS server
	 * @return true if IIS server, else false
  	 * \static
	 */
	static function hasIIS_UrlRewriteModule()
	{
		return isset($_SERVER['IIS_UrlRewriteModule']) == true;
	}

	static function getCredentialsFile()
	{
		global $default_api_tiki, $api_tiki, $db_tiki, $dbversion_tiki, $host_tiki, $user_tiki, $pass_tiki, $dbs_tiki, $tikidomain, $tikidomainslash, $dbfail_url;
		// Please use the local.php file instead containing these variables
		// If you set sessions to store in the database, you will need a local.php file
		// Otherwise you will be ok.
		//$api_tiki		= 'pear';
		//$api_tiki			= 'pdo';
		$api_tiki			= 'pdo';
		$db_tiki			= 'mysql';
		$dbversion_tiki = '2.0';
		$host_tiki		= 'localhost';
		$user_tiki		= 'root';
		$pass_tiki		= '';
		$dbs_tiki			= 'tiki';
		$tikidomain		= '';
		$dbfail_url		= '';

		/*
		SVN Developers: Do not change any of the above.
		Instead, create a file, called db/local.php, containing any of
		the variables listed above that are different for your
		development environment.  This will protect you from
		accidentally committing your username/password to SVN!

		example of db/local.php
		<?php
		$host_tiki   = 'myhost';
		$user_tiki   = 'myuser';
		$pass_tiki   = 'mypass';
		$dbs_tiki    = 'mytiki';
		$api_tiki    = 'adodb';

		** Multi-tiki
		**************************************
		see http://tikiwiki.org/MultiTiki19

		Setup of virtual tikis is done using setup.sh script
		-----------------------------------------------------------
		-> Multi-tiki trick for virtualhosting

		$tikidomain variable is set to :
		or TIKI_VIRTUAL
			That is set in apache virtual conf : SetEnv TIKI_VIRTUAL myvirtual
		or SERVER_NAME
			From apache directive ServerName set for that virtualhost block
		or HTTP_HOST
			From the real domain name called in the browser
			(can be ServerAlias from apache conf)

		*/

		if (!isset($local_php) or !is_file($local_php)) {
			$local_php = 'db/local.php';
		} else {
			$local_php = preg_replace(array('/\.\./', '/^db\//'), array('',''), $local_php);
		}
		$tikidomain = '';
		if (is_file('db/virtuals.inc')) {
			if (isset($_SERVER['TIKI_VIRTUAL']) and is_file('db/'.$_SERVER['TIKI_VIRTUAL'].'/local.php')) {
				$tikidomain = $_SERVER['TIKI_VIRTUAL'];
			} elseif (isset($_SERVER['SERVER_NAME']) and is_file('db/'.$_SERVER['SERVER_NAME'].'/local.php')) {
				$tikidomain = $_SERVER['SERVER_NAME'];
			} else if (isset($_REQUEST['multi']) && is_file('db/'.$_REQUEST['multi'].'/local.php')) {
				$tikidomain = $_REQUEST['multi'];
			} elseif (isset($_SERVER['HTTP_HOST'])) {
				if (is_file('db/'.$_SERVER['HTTP_HOST'].'/local.php')) {
					$tikidomain = $_SERVER['HTTP_HOST'];
				} else if (is_file('db/'.preg_replace('/^www\./', '', $_SERVER['HTTP_HOST']).'/local.php')) {
					$tikidomain = preg_replace('/^www\./', '', $_SERVER['HTTP_HOST']);
				}
			}
			if (!empty($tikidomain)) {
				$local_php = "db/$tikidomain/local.php";
			}
		}
		$tikidomainslash = (!empty($tikidomain) ? $tikidomain . '/' : '');

		$default_api_tiki = $api_tiki;
		$api_tiki = '';

		return $local_php;
	}

	static function getEnvironmentCredentials()
	{
		// Load connection strings from environment variables, as used by Azure and possibly other hosts
		$connectionString = null;
		foreach (array('MYSQLCONNSTR_Tiki', 'MYSQLCONNSTR_DefaultConnection') as $envVar) {
			if (isset($_SERVER[$envVar])) {
				$connectionString = $_SERVER[$envVar];
				continue;
			}
		}

		if ($connectionString && preg_match('/^Database=(?P<dbs>.+);Data Source=(?P<host>.+);User Id=(?P<user>.+);Password=(?P<pass>.+)$/', $connectionString, $parts)) {
			$parts['charset'] = 'utf8';
			$parts['socket'] = null;
			return $parts;
		}
		return null;
	}
}

/**
 * set how Tiki will report Errors
 * @param $errno
 * @param $errstr
 * @param $errfile
 * @param $errline
 */
function tiki_error_handling($errno, $errstr, $errfile, $errline)
{
	global $prefs, $phpErrors;

	if ( 0 === error_reporting() ) {
		// This error was triggered when evaluating an expression prepended by the at sign (@) error control operator, but since we are in a custom error handler, we have to ignore it manually.
		// See http://ca3.php.net/manual/en/language.operators.errorcontrol.php#98895 and http://php.net/set_error_handler
		return;
	}

	$err[E_ERROR]           = 'E_ERROR';
	$err[E_CORE_ERROR]      = 'E_CORE_ERROR';
	$err[E_USER_ERROR]      = 'E_USER_ERROR';
	$err[E_COMPILE_ERROR]   = 'E_COMPILE_ERROR';
	$err[E_WARNING]         = 'E_WARNING';
	$err[E_CORE_WARNING]    = 'E_CORE_WARNING';
	$err[E_USER_WARNING]    = 'E_USER_WARNING';
	$err[E_COMPILE_WARNING] = 'E_COMPILE_WARNING';
	$err[E_PARSE]           = 'E_PARSE';
	$err[E_NOTICE]          = 'E_NOTICE';
	$err[E_USER_NOTICE]     = 'E_USER_NOTICE';
	$err[E_STRICT]          = 'E_STRICT';

	if ( !defined('E_RECOVERABLE_ERROR') ) define('E_RECOVERABLE_ERROR', 4096);
	$err[E_RECOVERABLE_ERROR] = 'E_RECOVERABLE_ERROR';

	if ( !defined('E_DEPRECATED') ) define('E_DEPRECATED', 8192);
	$err[E_DEPRECATED] = 'E_DEPRECATED';

	if ( !defined('E_USER_DEPRECATED') ) define('E_USER_DEPRECATED', 16384);
	$err[E_USER_DEPRECATED] = 'E_USER_DEPRECATED';

	global $tikipath;
	$errfile = str_replace($tikipath, '', $errfile);
	switch ($errno) {
	case E_ERROR:
	case E_CORE_ERROR:
	case E_USER_ERROR:
	case E_COMPILE_ERROR:
	case E_WARNING:
	case E_CORE_WARNING:
	case E_USER_WARNING:
	case E_COMPILE_WARNING:
	case E_PARSE:
	case E_RECOVERABLE_ERROR:
		$back = "<div class='rbox-data' style='font-size:10px;border:1px solid'>";
		$back.= "<b>PHP (".PHP_VERSION.") ERROR (".$err[$errno]."):</b><br />";
		$back.= "<b style='font-family: monospace'>File:</b> $errfile<br />";
		$back.= "<b style='font-family: monospace'>Line:</b> $errline<br />";
		$back.= "<b style='font-family: monospace'>Type:</b> $errstr";
		$back.= "</div>";
		$phpErrors[] = $back;
    	break;
	case E_STRICT:
	case E_NOTICE:
	case E_USER_NOTICE:
	case E_DEPRECATED:
	case E_USER_DEPRECATED:
		if (!  defined('THIRD_PARTY_LIBS_PATTERN') ||  ! preg_match(THIRD_PARTY_LIBS_PATTERN, $errfile) ) {
			if ( ! empty($prefs['smarty_notice_reporting']) && $prefs['smarty_notice_reporting'] != 'y' && strstr($errfile, '.tpl.php'))
				break;
			$back = "<div class='rbox-data' style='font-size:10px;border:1px solid'>";
			$back.= "<b>PHP (".PHP_VERSION.") NOTICE ($err[$errno]):</b><br />";
			$back.= "<b style='font-family: monospace'>File:</b> $errfile<br />";
			$back.= "<b style='font-family: monospace'>Line:</b> $errline<br />";
			$back.= "<b style='font-family: monospace'>Type:</b> $errstr";
			$back.= "</div>";
			$phpErrors[] = $back;
		}
		break;
	default:
    	break;
	}
}

// Patch missing $_SERVER['REQUEST_URI'] on IIS6
if (empty($_SERVER['REQUEST_URI'])) {
	if (TikiInit::isIIS()) {
		$_SERVER['REQUEST_URI'] = $_SERVER['SCRIPT_NAME'];
	}
}

