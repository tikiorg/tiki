<?php

function smarty_modifier_star($score)
{
    global $feature_score, $scorelib;

    if ($feature_score != 'y') {
	return '';
    }

    require_once('lib/score/scorelib.php');
    return $scorelib->get_star($score);
}

/* vim: set expandtab: */

?>
