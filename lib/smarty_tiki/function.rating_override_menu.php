<?php
// (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function smarty_function_rating_override_menu( $params, $smarty )
{
	global $ratinglib;
	require_once("lib/rating/ratinglib.php");

	require_once('lib/comments/commentslib.php');
	$menu = '';
	$options = $ratinglib->override_array($params['type']);
	$optionsLength = count($options);
	foreach($options as $i => $option)
	{
		$menu .= "<option value='$i'" . ($i >= $optionsLength -1 ? ' selected'  : '') . ">" . $option . "</option>";
	}

	return "<select name='rating_override[]'>" . $menu . "</select>";
}

