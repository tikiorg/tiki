<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function smarty_function_error_report($params, $smarty)
{
	$errorreportlib = TikiLib::lib('errorreport');
	$errors = $errorreportlib->get_errors();
	
	if (count($errors)) {
		require_once 'lib/smarty_tiki/block.remarksbox.php';

		return smarty_block_remarksbox(array(
			'type' => 'errors',
			'title' => tra('Error(s)'),
		), '<ul><li>' . implode('</li><li>', $errors) . '</li></ul>', $smarty);
	}
}

