<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Search_GlobalSource_UrlSource implements Search_GlobalSource_Interface
{
	function __construct()
	{
		$smarty = TikiLib::lib('smarty');
		$smarty->loadPlugin('smarty_modifier_sefurl');
	}

	function getData($objectType, $objectId, Search_Type_Factory_Interface $typeFactory, array $data = array())
	{
		if (isset($data['url'])) {
			return false;
		}

		$url = smarty_modifier_sefurl($objectId, $objectType);
		return array(
			'url' => $typeFactory->identifier($url),
		);
	}

	function getProvidedFields()
	{
		return array(
			'url',
		);
	}
	
	function getGlobalFields()
	{
		return array(
		);
	}
}

