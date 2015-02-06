<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

/**
 * Handler class for PageSelector
 *
 * Letter key: ~k~
 * Possibly doesn't need "non-simple" handling apart from defaultvalue?
 *
 */
class Tracker_Field_PageSelector extends Tracker_Field_Abstract
{
	public static function getTypes()
	{
		return array(
			'k' => array(
				'name' => tr('Page Selector'),
				'description' => tr('Allows a selection from the list of pages.'),
				'help' => 'Page selector',
				'prefs' => array('trackerfield_pageselector', 'feature_wiki'),
				'tags' => array('advanced'),
				'default' => 'y',
				'params' => array(
					'autoassign' => array(
						'name' => tr('Auto-Assign'),
						'description' => tr('Will auto-assign the creator of the item.'),
						'filter' => 'int',
						'options' => array(
							0 => tr('No'),
							1 => tr('Yes'),
						),
						'legacy_index' => 0,
					),
					'size' => array(
						'name' => tr('Display Size'),
						'description' => tr('Visible size of the input in characters.'),
						'filter' => 'int',
						'legacy_index' => 1,
					),
					'create' => array(
						'name' => tr('Create Page'),
						'description' => tr('Create missing pages using the page name in this file as the template.'),
						'filter' => 'pagename',
						'legacy_index' => 2,
						'profile_reference' => 'wiki_page',
					),
					'link' => array(
						'name' => tr('Link'),
						'description' => tr('Display the value as a link to the page'),
						'filter' => 'alpha',
						'default' => 'y',
						'options' => array(
							'y' => tr('Yes'),
							'n' => tr('No'),
						),
						'legacy_index' => 3,
					),
				),
			),
		);
	}

	function getFieldData(array $requestData = array())
	{
		$ins_id = $this->getInsertId();

		return array(
			'value' => isset($requestData[$ins_id])
				? $requestData[$ins_id]
				: $this->getValue(),
			'defaultvalue' => $this->getOption('create')
				? $this->getOption('create')
				: $this->getValue(),
		);
	}

	function renderInput($context = array())
	{
		return $this->renderTemplate('trackerinput/pageselector.tpl', $context);
	}

	function renderOutput($context = array())
	{
		$value = $this->getConfiguration('value');
		if ($value) {
			if ($this->getOption('link') === 'n' || $context['list_mode'] === 'csv') {
				return $value;
			} else {
				$smarty = TikiLib::lib('smarty');
				$smarty->loadPlugin('smarty_function_object_link');
				return smarty_function_object_link(
					array(
						'type' => 'wikipage',
						'id' => $value,
					),
					$smarty
				);
			}
		}
	}
}

