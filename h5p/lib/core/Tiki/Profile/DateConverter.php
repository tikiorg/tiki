<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Tiki_Profile_DateConverter
{
	function convert( $value )
	{
		if ( is_int($value) )
			return $value;

		$time = strtotime($value);
		if ( $time !== false )
			return $time;
	}
}
