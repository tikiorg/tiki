<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

/**
 *
 */
class ErrorReportLib
{
    /**
     * @param $message
     */
    function report($message)
	{
		if (! isset($_SESSION['errorreport'])) {
			$_SESSION['errorreport'] = array();
		}

		$_SESSION['errorreport'][] = $message;
	}

    /**
     * @return array
     */
    function get_errors()
	{
		if (! isset($_SESSION['errorreport'])) {
			return array();
		}

		$errors = $_SESSION['errorreport'];
		unset($_SESSION['errorreport']);

		return $errors;
	}

	function send_headers()
	{
		require_once 'lib/smarty_tiki/function.error_report.php';
		header('X-Tiki-Error: ' . str_replace(array("\n", "\r", "\t"), '', smarty_function_error_report(array(), TikiLib::lib('smarty'))));
	}
}

