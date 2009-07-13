<?php
/*
 * A set of unit test for Workspaces
 *
 * NOTE: If you want test WS quickly, use scriptCreator instead.
 * This is only for testing.
 *
 */

require_once '../../../../tiki-setup.php';
require_once 'lib/workspaces/wslib.php';

if (!isset($_REQUEST['quantity'])) $testQuantity = 1000;
else $testQuantity = $_REQUEST['quantity'];

echo "<h1>--- Unit Test of Workspaces ---</h1>";
echo "testQuantity = $testQuantity";

echo <<<END
<form action="wsInsertionTest.php" method="post">
<input type="submit" name="Reload Test" value="Reload Test" />
</form>
END;

echo "<h3>--- Adding WS using ws_add ---</h3>";

global $prefs;
$ws_container = $prefs['ws_container'];

for ($i=0; $i<$testQuantity; $i++)
{
    $time = (string) microtime();
    $hashtime = md5($time);
    $wslib->create_ws($hashtime,"Registered", null, true);
}

echo "Check the database tables tiki_categories <br />";
echo "Done";

echo "<br /><a href=\"../unitesting.php\">Back to Unit testing</a>";
