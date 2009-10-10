<?php

/* Check access */
require_once ('tiki-setup.php');
$access->check_script($_SERVER["SCRIPT_NAME"],basename(__FILE__));

/* Rest of imports */
global $userlib; require_once 'lib/userslib.php';
global $wslib; require_once 'lib/workspaces/wslib.php';

ask_ticket('admin-inc-workspaces');
