<?php
// (c) Copyright 2002-2013 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Search_Formatter_ValueFormatter_Wikiplugin extends Search_Formatter_ValueFormatter_Abstract
{
	private $arguments;

	function __construct( $arguments )
	{
		$this->arguments = $arguments;
	}

	function render($name, $value, array $entry)
	{
		if (substr($name, 0, 11) !== 'wikiplugin_') {
			return $value;
		} else {
			$name = substr($name, 11);
		}

		if (isset($this->arguments['content'])) {
			$content = $this->arguments['content'];
			unset( $this->arguments['content'] );
		} else {
			$content = '';
		}

		$params = array();
		foreach ($this->arguments as $key => $val) {
			$params[$key] = $entry[$val];
		}

		$parserlib = TikiLib::lib('parser');
		$out = $parserlib->plugin_execute(
			$name,
			$content,
			$params,
			0,
			false,
			array(
				'context_format' => 'html',
				'ck_editor' => false,
			)
		);

		return '~np~' . $out . '~/np~';
	}
}

