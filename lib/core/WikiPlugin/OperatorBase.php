<?php
// (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

abstract class WikiPlugin_OperatorBase
{
	var $name;
	var $documentation;
	var $description;
	var $prefs;
	var $body;
	var $validate;
	var $filter = 'rawhtml_unsafe';
	var $icon = 'img/icons/mime/html.png';
	var $tags = array( 'basic' );
	var $params = array(

	);

	function output($data, $params, $index, $parser)
	{

	}
}