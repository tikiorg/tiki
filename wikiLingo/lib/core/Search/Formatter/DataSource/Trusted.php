<?php
// (c) Copyright 2002-2013 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

/**
 * ElasticSearch provides the raw documents as part of the results, so no need to re-fetch the information.
 */
class Search_Formatter_DataSource_Trusted implements Search_Formatter_DataSource_Interface
{
	function addContentSource($type, Search_ContentSource_Interface $contentSource)
	{
	}

	function addGlobalSource(Search_GlobalSource_Interface $globalSource)
	{
	}

	function getInformation(Search_ResultSet $list, array $fields)
	{
		return $list;
	}
}

