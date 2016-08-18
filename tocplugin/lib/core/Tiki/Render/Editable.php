<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Tiki_Render_Editable
{
	private $inner;
	private $layout = 'inline';
	private $group = false;
	private $label = null;
	private $fieldFetchUrl;
	private $objectStoreUrl;

	function __construct($html, array $parameters)
	{
		$this->inner = $html;
		
		if (! empty($parameters['layout']) && in_array($parameters['layout'], array('inline', 'block', 'dialog'))) {
			$this->layout = $parameters['layout'];
		}

		if (! empty($parameters['group'])) {
			$this->group = $parameters['group'];
		}

		if (! empty($parameters['label'])) {
			$this->label = $parameters['label'];
		}

		if (empty($parameters['object_store_url'])) {
			throw new Exception(tr('Internal error: mandatory parameter object_store_url is missing'));
		}

		$servicelib = TikiLib::lib('service');
		if (! empty($parameters['field_fetch_url'])) {
			$this->fieldFetchUrl = $parameters['field_fetch_url'];
		}

		$this->objectStoreUrl = $parameters['object_store_url'];
	}

	function __toString()
	{
		global $prefs;
		TikiLib::lib('smarty')->loadPlugin('smarty_modifier_escape');

		if ($prefs['ajax_inline_edit'] != 'y') {
			return $this->inner === null ? '' : $this->inner;
		}

		// block = dialog goes to span as well
		$tag = ($this->layout == 'block') ? 'div' : 'span';
		$fieldFetch = smarty_modifier_escape(json_encode($this->fieldFetchUrl));
		$objectStore = smarty_modifier_escape(json_encode($this->objectStoreUrl));
		$label = smarty_modifier_escape($this->label);

		$value = $this->inner;
		if (trim(strip_tags($value)) == '') {
			// When the value is empty, make sure it becomes visible/clickable
			$value .= '&nbsp;';
		}

		$class = "editable-inline";
		if ($this->layout == 'dialog') {
			$class = "editable-dialog";
		}

		if (! $this->fieldFetchUrl) {
			$class .= ' loaded';
		}

		$group = smarty_modifier_escape($this->group);
		return "<$tag class=\"$class\" data-field-fetch-url=\"$fieldFetch\" data-object-store-url=\"$objectStore\" data-group=\"$group\" data-label=\"$label\">$value</$tag>";
	}
}

