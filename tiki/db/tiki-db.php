<?php
// Database connection for the tiki system
require_once('DB.php');
$host_tiki   = 'localhost';
$user_tiki   = 'root';
$pass_tiki   = 'seldon';
$dbs_tiki    = 'tiki';

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

?>
