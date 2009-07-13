<?php
/*
 * A set of unit test for Workspaces
 *
 * NOTE: If you want test WS quickly, use scriptCreator instead.
 * This is only for testing.
 *
 */

require_once '../../../tiki-setup.php';
require_once 'lib/workspaces/wslib.php';

$testQuantity = 1000;

echo "<h2>--- Unit Test of Workspaces ---</h2>";
echo "testQuantity = $testQuantity";

echo <<<END
<form action="unitesting.php" method="post">
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
    $wslib->create_ws($hashtime,$hashtime);
}


echo "Done";
