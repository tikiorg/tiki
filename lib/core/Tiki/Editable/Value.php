<?php
// (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Tiki_Editable_Value
{
	private $inner;
	private $layout = 'inline';
	private $family = 'editable-value';
	private $fieldFetchUrl;
	private $objectStoreUrl;

	function __construct($html, array $parameters)
	{
		$this->inner = $html;
		
		if (! empty($parameters['layout']) && in_array($parameters['layout'], array('inline', 'block'))) {
			$this->layout = $parameters['layout'];
		}

		if (! empty($parameters['family'])) {
			$this->family = $parameters['family'];
		}

		if (empty($parameters['field_fetch_url'])) {
			throw new Exception(tr('Internal error: mandatory parameter field_fetch_url is missing'));
		}

		if (empty($parameters['object_store_url'])) {
			throw new Exception(tr('Internal error: mandatory parameter object_store_url is missing'));
		}

		$this->fieldFetchUrl = $parameters['field_fetch_url'];
		$this->objectStoreUrl = $parameters['object_store_url'];
	}

	function __toString()
	{
		global $prefs;

		if ($prefs['ajax_inline_edit'] != 'y') {
			return $this->inner;
		}

		$tag = ($this->layout == 'block') ? 'div' : 'span';
		$fieldFetch = htmlspecialchars($this->fieldFetchUrl);
		$objectStore = htmlspecialchars($this->objectStoreUrl);
		$family = htmlspecialchars($this->family);

		$value = $this->inner;
		if (trim(strip_tags($value)) == '') {
			// When the value is empty, make sure it becomes visible/clickable
			$value .= '&nbsp;';
		}

		return "<$tag class=\"editable-inline\" data-edit-family=\"$family\" data-field-fetch-url=\"$fieldFetch\" data-object-store-url=\"$objectStore\">$value</$tag>";
	}
}

