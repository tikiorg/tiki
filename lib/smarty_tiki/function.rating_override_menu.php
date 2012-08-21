<?php
// (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function smarty_function_rating_override_menu( $params, $smarty )
{
	global $prefs, $headerlib, $ratinglib;
	require_once("lib/rating/ratinglib.php");

	require_once('lib/comments/commentslib.php');
	$menu = '';
	$options = $ratinglib->override_array($params['type'], true);
	$optionsLength = count($options);
	$backgrounds = $ratinglib->get_options_smiles_backgrounds($params['type']);

	foreach($options as $i => $option)
	{
		if ($prefs['rating_smileys'] == 'y') {
			$style = 'background-image: url("' . $backgrounds[$i] . '");';
			$text = count($option);
		} else {
			$style = '';
			$text = implode(',', $option);
		}

		$menu .= "<option style='" . $style . "' value='$i'" . ($i >= $optionsLength -1 ? ' selected'  : '') . ">" . $text . "</option>";
	}

	return "<select class='rating_override_selector' style='width: 250px;' name='rating_override[]'>" . $menu . "</select>";
}

