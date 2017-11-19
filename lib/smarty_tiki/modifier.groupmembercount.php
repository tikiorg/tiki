<?php

if (strpos($_SERVER["SCRIPT_NAME"], basename(__FILE__)) !== false) {
	header("location: index.php");
	exit;
}

function smarty_modifier_groupmembercount($group)
{
	return TikiLib::lib('user')->nb_users_in_group($group);
}
