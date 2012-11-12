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
	private $editUrl;

	function __construct($html, array $parameters)
	{
		$this->inner = $html;
		
		if (! empty($parameters['layout']) && in_array($parameters['layout'], array('inline', 'block'))) {
			$this->layout = $parameters['layout'];
		}

		if (! empty($parameters['family'])) {
			$this->family = $parameters['family'];
		}

		if (empty($parameters['edit_url'])) {
			throw new Exception(tr('Internal error: mandatory parameter edit_url is missing'));
		}

		$this->editUrl = $parameters['edit_url'];
	}

	function __toString()
	{
		global $prefs;

		if ($prefs['ajax_inline_edit'] != 'y') {
			return $this->inner;
		}

		$tag = ($this->layout == 'block') ? 'div' : 'span';
		$url = htmlspecialchars($this->editUrl);
		$family = htmlspecialchars($family);

		return "<$tag class=\"editable-inline\" data-edit-family=\"$family\" data-edit-url=\"$url\">{$this->inner}</$tag>";
	}
}

