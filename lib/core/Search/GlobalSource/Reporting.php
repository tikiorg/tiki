<?php
// (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Search_GlobalSource_Reporting implements Search_GlobalSource_Interface
{
	private $data = array();

	function getProvidedFields()
	{
		return array('report_status');
	}

	function getGlobalFields()
	{
		return array();
	}

	function setStatus($objectType, $objectId, $success)
	{
		$this->data["$objectType:$objectId"] = $success ? 'success' : 'error';
	}

	function getData($objectType, $objectId, Search_Type_Factory_Interface $typeFactory, array $data = array())
	{
		$identifier = "$objectType:$objectId";
		return array(
			'report_status' => $typeFactory->identifier(isset($this->data[$identifier]) ? $this->data[$identifier] : 'none'),
		);
	}
}

