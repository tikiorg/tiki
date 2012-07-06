<?php
// (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

abstract class WikiPlugin_ConditionBase
{
	var $name;
	var $type;
	var $documentation;
	var $description;
	var $format;
	var $prefs;
	var $body;
	var $validate;
	var $filter = 'rawhtml_unsafe';
	var $icon = 'img/icons/mime/html.png';
	var $tags = array( 'basic' );
	var $params = array(

	);

	var $np = true;

	public function info()
	{
		$info = array();
		foreach($this as $key => $param)
		{
			$info[$key] = $param;
		}

		return $info;
	}

	protected function paramDefaults(&$params)
	{
		$defaults = array();
		foreach($this->params as $param => $setting) {
			if (!empty($setting)) {
				$defaults[$param] = $setting;
			}
		}

		$params = array_merge($defaults, $params);
	}

	abstract protected function output(&$data, &$params, &$index, &$parser);

	public function exec($data, $params, $index, $parser)
	{
		$this->paramDefaults($params);

		// strip out sanitisation which may have occurred when using nested plugins
		$data = str_replace('<x>', '', $data);
		$data = $this->output($data, $params, $index, $parser);

		if ($this->np) {
			return '~np~'.$data.'~/np~';
		} else {
			return $data;
		}
	}
}