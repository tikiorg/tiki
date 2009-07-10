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
$wsnames = array();

for ($i=0; $i<$testQuantity; $i++)
{
    $time = (string) time();
    $hashtime = md5($hashtime);
    $wslib->add_ws($hashtime, $ws_container, $hashtime);
    $wsnames[$i] = $hashtime;
}

$wslib->add_ws("workspace1", $ws_container, "biologia");
$wslib->add_ws("workspace2", $ws_container, "biologia");

echo "Done";

echo "<h3>--- Gettin some WS using get_ws_id ---</h3>";

echo "<ul>";
for ($i=0; $i<$testQuantity; $i++)
{
    echo "<li>".$wslib->get_ws_id($wsnames[$i], (int) $ws_container)."</li>";
}
echo "</ul>";
