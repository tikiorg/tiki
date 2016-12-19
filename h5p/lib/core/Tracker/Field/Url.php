<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

/**
 * Handler class for url fields:
 *
 * - url key ~L~
 */
class Tracker_Field_Url extends Tracker_Field_Abstract implements Tracker_Field_Synchronizable, Tracker_Field_Exportable
{
	public static function getTypes()
	{
		return array(
			'L' => array(
				'name' => tr('URL'),
				'description' => tr('Creates a link to a specified URL.'),
				'help' => 'URL Tracker Field',
				'prefs' => array('trackerfield_url'),
				'tags' => array('basic'),
				'default' => 'y',
				'params' => array(
					'linkToURL' => array(
						'name' => tr('Display'),
						'description' => tr('How the URL should be rendered'),
						'filter' => 'int',
						'options' => array(
							0 => tr('URL as link'),
							1 => tr('Plain text'),
							2 => tr('Site title as link'),
							3 => tr('URL as link plus site title'),
							4 => tr('Text as link (see Other)'),
						),
						'legacy_index' => 0,
						'default' => 0,
					),
					'other' => array(
						'name' => tr('Other'),
						'description' => tr('Label of the link text. Requires "Display" to be set to "Text as link"'),
						'filter' => 'text',
						'default' => '',
					),
				),
			),
		);
	}

	function getFieldData(array $requestData = array())
	{
		$ins_id = $this->getInsertId();

		return array(
			'value' => (isset($requestData[$ins_id]))
				? $requestData[$ins_id]
				: $this->getValue(),
		);
	}

	function renderOutput($context = array())
	{
		$smarty = TikiLib::lib('smarty');

		$url = $this->getConfiguration('value');

		if (empty($url) || $context['list_mode'] == 'csv' || $this->getOption('linkToURL') == 1 ) {
			return $url;
		} elseif ($this->getOption('linkToURL') == 2) { // Site title as link
			$smarty->loadPlugin('smarty_function_object_link');
			return smarty_function_object_link(
				array(
					'type' => 'external',
					'id' => $url,
				),
				$smarty
			);
		} elseif ($this->getOption('linkToURL') == 0) { // URL as link
			$parsedUrl = trim(str_replace('<br />', '', TikiLib::lib('tiki')->parse_data($url)));
			if ($parsedUrl != $url) {
				return $parsedUrl;
			}
			$smarty->loadPlugin('smarty_function_object_link');
			return smarty_function_object_link(
				array(
					'type' => 'external',
					'id' => $url,
					'title' => $url,
				),
				$smarty
			);
		} elseif ($this->getOption('linkToURL') == 3) { // URL + site title
			$smarty->loadPlugin('smarty_function_object_link');
			return smarty_function_object_link(
				array(
					'type' => 'external_extended',
					'id' => $url,
				),
				$smarty
			);
		} elseif ($this->getOption('linkToURL') == 4) { // URL as link
			$smarty->loadPlugin('smarty_function_object_link');
			return smarty_function_object_link(
				array(
					'type' => 'external',
					'id' => $url,
					'title' => tr($this->getOption('other')),
				),
				$smarty
			);
		} else {
			return $url;
		}
	}

	function renderInput($context = array())
	{
		return $this->renderTemplate("trackerinput/url.tpl", $context);
	}

	function importRemote($value)
	{
		return $value;
	}

	function exportRemote($value)
	{
		return $value;
	}

	function importRemoteField(array $info, array $syncInfo)
	{
		return $info;
	}

	function getTabularSchema()
	{
		$schema = new Tracker\Tabular\Schema($this->getTrackerDefinition());

		$permName = $this->getConfiguration('permName');
		$name = $this->getConfiguration('name');

		$schema->addNew($permName, 'default')
			->setLabel($name)
			->setRenderTransform(function ($value) {
				return $value;
			})
			->setParseIntoTransform(function (& $info, $value) use ($permName) {
				$info['fields'][$permName] = $value;
			});

		return $schema;
	}

}

