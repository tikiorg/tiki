<?php
// (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Services_Workspace_Controller
{
	private $utilities;

	function setUp()
	{
		$this->utilities = new Services_Workspace_Utilities;

		Services_Exception_Disabled::check('workspace_ui');
		Services_Exception_Disabled::check('feature_perspective');
		Services_Exception_Disabled::check('namespace_enabled');
		Services_Exception_Disabled::check('feature_categories');
	}

	function action_create($input)
	{
		global $prefs;
		$templates = $this->utilities->getTemplateList();

		if (0 === count($templates)) {
			throw new Services_Exception_Denied('No templates available to you');
		}

		$name = $input->name->text();
		$template = $input->template->int();

		if ($template && $name) {
			$perms = Perms::get('workspace', $template);

			if (! $perms->workspace_instantiate) {
				throw new Services_Exception_Denied;
			}

			$transaction = TikiDb::get()->begin();
			$parts = explode($prefs['namespace_separator'], $name);

			$this->utilities->validateCategory($parts);
			$this->utilities->validatePerspective($name);
			$this->utilities->validatePage($name);
			$this->utilities->validateGroup($name);

			$values = array(
				'category' => $this->utilities->createCategory($parts),
				'perspective' => $this->utilities->createPerspective($name),
				'page' => $this->utilities->createPage($name),
				'group' => $this->utilities->createGroup($name),
			);

			$this->utilities->initialize($values);

			$transaction->commit();
		}

		return array(
			'templates' => $templates,
		);
	}

	function action_list_templates($input)
	{
		return array(
			'list' => $this->utilities->getTemplateList(),
		);
	}

	function action_add_template($input)
	{
		if (! Perms::get()->admin) {
			throw new Services_Exception_Denied;
		}

		$id = null;
		if ($input->name->text()) {
			$id = $this->utilities->addTemplate(array(
				'name' => $input->name->text(),
			));
		}

		return array(
			'id' => $id,
		);
	}
}

