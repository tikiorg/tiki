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

$testQuantity = 2000;

echo "<h2>--- Unit Test of Workspaces ---</h2>";
echo "testQuantity = $testQuantity";

echo "<h3>--- Adding WS using ws_add ---</h3>";

global $prefs;
$ws_container = $prefs['ws_container'];

echo "<ul>";

for ($i=0; $i<$testQuantity;$i++)
{
    $hashtime = (string) time();
    $group = (string) (time()+time());
    $hash = md5($hashtime);
    $hashgroup = md5($group);
    $wslib->add_ws($hash, $ws_container, $hashgroup);
    echo "<li>$i - $hash - $ws_container - $hashgroup</li>";
}

echo "</ul>";

