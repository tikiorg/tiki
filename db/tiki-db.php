<?php
$separator='';
$current_path=ini_get('include_path');
if(strstr($current_path, ';')) {
	$separator=';'; 
} else {
	$separator=':'; 
}
if($separator=='') $separator = ':'; // guess
ini_set('include_path', $current_path.$separator.'lib/pear');

// Database connection for the tiki system
require_once('DB.php');
$host_tiki   = 'localhost';
$user_tiki   = 'root';
$pass_tiki   = '';
$dbs_tiki    = 'tiki16';

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
\$host_tiki   = 'myhost';
\$user_tiki   = 'myuser';
\$pass_tiki   = 'mypass';
\$dbs_tiki    = 'mytiki';
?>
EOF
*/

$file = dirname(__FILE__) . '/local.php';
if (file_exists($file))
	require_once($file);

$dsn = "mysql://$user_tiki:$pass_tiki@$host_tiki/$dbs_tiki";    
//$dsn = "mysql://$user_tiki@$pass_tiki(localhost)/$dbs_tiki";
$dbTiki = DB::connect($dsn);
if (DB::isError($dbTiki)) {        
  die ($dbTiki->getMessage());
} 

// Forget db info so that malicious PHP may not get password etc.
$host_tiki   = NULL;
$user_tiki   = NULL;
$pass_tiki   = NULL;
$dbs_tiki    = NULL;

unset($host_tiki);
unset($user_tiki);
unset($pass_tiki);
unset($dbs_tiki);

?>
