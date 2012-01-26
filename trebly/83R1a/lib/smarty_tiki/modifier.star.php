<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: modifier.star.php 33195 2011-03-02 17:43:40Z changi67 $

function smarty_modifier_star($score)
{
	global $prefs, $tikilib;

	if ($prefs['feature_score'] != 'y') {
		return '';
	}

	return $tikilib->get_star($score);
}
