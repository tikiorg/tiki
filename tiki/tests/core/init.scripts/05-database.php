<?php
/**
 * \file
 * $Header: /cvsroot/tikiwiki/tiki/tests/core/init.scripts/05-database.php,v 1.1 2003-08-22 19:04:40 zaufi Exp $
 *
 * \brief Initialize low level DB support
 */

if ($api_tiki == 'adodb')
{
    error_reporting (E_ALL);       # show any error messages triggered
    define('ADODB_FORCE_NULLS', 1);
    define('ADODB_ASSOC_CASE', 2);
    define('ADODB_CASE_ASSOC', 2); // typo in adodb's driver for sybase?
    include_once ('adodb.inc.php');
    //include_once('adodb-error.inc.php');
    //include_once('adodb-errorhandler.inc.php');
    //include_once('adodb-errorpear.inc.php');
    include_once ('adodb-pear.inc.php');

    if ($db_tiki == 'pgsql') {
        $db_tiki = 'postgres7';
    }

    if ($db_tiki == 'sybase') {
        // avoid database change messages
        ini_set('sybct.min_server_severity', '11');
    }

    $ADODB_FETCH_MODE = ADODB_FETCH_ASSOC;
// ADODB_FETCH_BOTH appears to be buggy for null values
} else {
    // Database connection for the tiki system
    include_once ('DB.php');
}

//doesn't work with adodb. adodb doesn't let you inherit
/*
class tikiDB extends ADOConnection {
  var $dbversion;
}
*/
$dsn = "$db_tiki://$user_tiki:$pass_tiki@$host_tiki/$dbs_tiki";
//$dsn = "mysql://$user_tiki@$pass_tiki(localhost)/$dbs_tiki";
$dbTiki = &ADONewConnection($db_tiki);

if (!$dbTiki->Connect($host_tiki, $user_tiki, $pass_tiki, $dbs_tiki)) {
    print "
<html><body>
<p>Unable to login to the MySQL database '$dbs_tiki' on '$host_tiki' as user '$user_tiki'<br />
<a href='tiki-install.php'>Go here to begin the installation process</a>, if you haven't done so already.</p>
</body></html>
";

    print $dbTiki->ErrorMsg();
    exit;
}

if ($db_tiki == 'sybase') {
    $dbTiki->Execute("set quoted_identifier on");
}

?>
