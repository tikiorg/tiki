<?php

function smarty_modifier_star($score)
{
    global $prefs, $tikilib;

    if ($prefs['feature_score'] != 'y') {
	return '';
    }

    return $tikilib->get_star($score);
}



?>
