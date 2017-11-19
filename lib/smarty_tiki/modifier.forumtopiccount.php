<?php

if (strpos($_SERVER["SCRIPT_NAME"], basename(__FILE__)) !== false) {
	header("location: index.php");
	exit;
}

function smarty_modifier_forumtopiccount($forumId)
{
	return TikiLib::lib('comments')->count_forum_topics($forumId);
}
