<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER['SCRIPT_NAME'], basename(__FILE__)) !== false) {
	header('location: index.php');
	exit;
}

class Services_Exception_BadRequest extends Services_Exception
{

	function __construct($message = null)
	{
		if (is_null($message)) {
			$message = tra('Bad request - possible cross-site request forgery (CSRF) detected. The action was blocked');
		}
		parent::__construct($message, 400);
	}

	public static function checkAccess($message = null)
	{
		$access = TikiLib::lib('access');
		$check = $access->check_authenticity(null, false);
		if ($check === false) {
			throw new self($message);
		} else {
			return $check;
		}
	}
}
