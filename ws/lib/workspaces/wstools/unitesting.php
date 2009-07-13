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

echo <<<END
<h1>Unit Test of WS</h1>
<form action="tests/wsInsertionTest.php" method="post">
<label for="quantity">Test Quantity (default 1000): </label><input type="text" name="quantity" />
<input type="submit" name="Insertion Test" value="Insertion Test" />
</form>
END;


