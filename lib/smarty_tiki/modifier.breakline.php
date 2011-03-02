<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

/**
 * This modifier allows the unique "[breakline]" into a string to split into separate lines replaced by <br /> after the use of escape modifier which unallows any html tag. 
 * usefull for <label> when they need to be long and/or when automatic breaklines particularly after translations creates a split line in an unapproriate place.
 *
 * @param mixed $str_content 
 * @access public
 * @return void
 */
function smarty_modifier_breakline($str_content)
{
	return str_replace('[breakline]', '<br />', $str_content);
}
