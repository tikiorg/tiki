<?php
$separator='';
$current_path=ini_get('include_path');
if(strstr($current_path, ';')) {
	$separator=';'; 
} else {
	$separator=':'; 
}
if($separator=='') $separator = ':'; // guess
ini_set('include_path', dirname(dirname(__FILE__)).'/lib/pear'.$separator.$current_path);

$api_tiki	 = 'pear';
#$api_tiki	 = 'adodb';
$db_tiki	 = 'mysql';
#$db_tiki	 = 'pgsql';
$host_tiki   = 'localhost';
$user_tiki   = 'root';
$pass_tiki   = '';
$dbs_tiki    = 'tiki';
$tikidomain  = '';

/*
CVS Developers: Do not change any of the above.
Instead, create a file, called local.php, containing any of
the variables listed above that are different for your 
development environment.  This will protect you from 
accidentally committing your username/password to CVS!

For example:

cd <tiki_dir>/db
cat >local.php <<EOF
<?php
\$api_tiki	  = 'pear';
\$db_tiki	  = 'mysql';
\$host_tiki   = 'myhost';
\$user_tiki   = 'myuser';
\$pass_tiki   = 'mypass';
\$dbs_tiki    = 'mytiki';
?>
EOF

Site admins: you can use multiple virtualhosts with the same documentroot
just changing database and $tikidomain that is used to split cache directories
(template_c and modules/cache). For example a local.php file :

$host_tiki   = 'localhost';
$user_tiki   = 'root';
$pass_tiki   = '';

$host_map = array(
	'feu.org' = array(
		'dbs_tiki'    => 'tiki_cvs',
		'tikidomain'  => 'feu',
	),
	
	'localis.org' = array(
		'dbs_tiki'    => 'tiki_localis',
		'tikidomain'  => 'localis',
	),
);

if (isset($host_map[$HTTP_HOST])) {
	extract($host_map[$HTTP_HOST]);
}

*/

$file = dirname(__FILE__) . '/local.php';
if (file_exists($file))
	require_once($file);

if (preg_match('/^adodb$/i', $api_tiki)) {
	ini_set('include_path', $current_path.$separator.'lib/adodb');
#	error_reporting(E_ALL); # show any error messages triggered
	include_once('adodb-errorpear.inc.php');
#	include_once('adodb-errorhandler.inc.php');
	include_once('adodb-pear.inc.php');
	if ($db_tiki == 'pgsql') {
		$db_tiki = 'postgres7';
	}
} else {
	// Database connection for the tiki system
	include_once('DB.php');
}

$dsn = "$db_tiki://$user_tiki:$pass_tiki@$host_tiki/$dbs_tiki";    
//$dsn = "mysql://$user_tiki@$pass_tiki(localhost)/$dbs_tiki";
$dbTiki = DB::connect($dsn);
if (DB::isError($dbTiki)) {        
  print "
<pre>
Unable to login to the mySQL database '$dbs_tiki' on '$host_tiki' as user '$user_tiki'
";
print $dbTiki->getMessage();
exit;
} 

// Forget db info so that malicious PHP may not get password etc.
$host_tiki   = NULL;
$user_tiki   = NULL;
$pass_tiki   = NULL;
$dbs_tiki    = NULL;

unset($host_map);
unset($api_tiki);
unset($db_tiki);
unset($host_tiki);
unset($user_tiki);
unset($pass_tiki);
unset($dbs_tiki);

?>
