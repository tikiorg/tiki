<?php
// Database connection for the tiki system
require_once('DB.php');
$host_tiki   = 'localhost';
$user_tiki   = 'root';
$pass_tiki   = '';
$dbs_tiki    = 'tiki14b1';

/*
To prevent others from discovering the above information, 
do not enter your username/password above.
Instead, create a php file containing the above four lines
and name the file using a cryptographically secure name
(do not use randomcharshere.php!).
Then remove read/write rights to the db/ directory.

Developers: This method allows you to change the username/password
without having to change this file 
(and potentially committing your username/password to CVS!).

For example:

cd <tiki_dir>/db
cat >randomcharshere.php <<EOF
<?php
\$host_tiki   = 'myhost';
\$user_tiki   = 'myuser';
\$pass_tiki   = 'mypass';
\$dbs_tiki    = 'mytiki';
?>
EOF
chmod go-rw+x .

Finally, change the following name to match the random filename 
you selected:
*/

$file = dirname(__FILE__) . '/randomcharshere.php';
if (file_exists($file))
	require_once($file);

$dsn = "mysql://$user_tiki:$pass_tiki@$host_tiki/$dbs_tiki";    
//$dsn = "mysql://$user_tiki@$pass_tiki(localhost)/$dbs_tiki";
$dbTiki = DB::connect($dsn);
if (DB::isError($dbTiki)) {        
  die ($dbTiki->getMessage());
} 

?>
