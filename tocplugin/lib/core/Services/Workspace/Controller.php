<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
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
			$templateInfo = $this->utilities->getTemplate($template);
			$perms = Perms::get('workspace', $template);

			if (! $perms->workspace_instantiate) {
				throw new Services_Exception_Denied;
			}
			$workspaceName = $name;
			$name = $templateInfo['name'] . $prefs['namespace_separator'] . $name;

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
			$values['namespace'] = $values['page'];

			$this->utilities->initialize($values);
			$this->utilities->applyTemplate($templateInfo, $values);

			$transaction->commit();
		}

		return array(
			'title' => tr('Create Workspace'),
			'templates' => $templates,
		);
	}

	function action_list_templates($input)
	{
		return array(
			'title' => tr('Workspace Templates'),
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
			$builder = new Services_Workspace_ProfileBuilder;
			$builder->addGroup('Base', $builder->user('group'));
			$builder->setManagingGroup('Base');
			$builder->setPermissions(
				'Base',
				'category',
				$builder->user('category'),
				array(
					'admin_cms',
					'blog_admin',
					'bigbluebutton_create',
					'bigbluebutton_moderate',
					'bigbluebutton_view_rec',
					'bigbluebutton_join',
					'admin_calendar',
					'admin_categories',
					'modify_object_categories',
					'admin_comments',
					'dsn_query',
					'admin_faqs',
					'admin_file_galleries',
					'admin_forum',
					'admin_freetags',
					'group_view',
					'group_view_members',
					'group_add_member',
					'group_remove_member',
					'group_join',
					'admin_galleries',
					'admin_newsletters',
					'payment_view',
					'payment_manual',
					'payment_request',
					'perspective_view',
					'vote_poll',
					'view_poll_voters',
					'admin_sheet',
					'take_survey',
					'view_survey_stats',
					'admin_trackers',
					'admin_wiki',
					'detach_translation',
					'site_report',
					'modify_object_categories',
					'use_references',
					'edit_references',
				)
			);
			$id = $this->utilities->replaceTemplate(
				0,
				array(
					'name' => $input->name->text(),
					'definition' => $builder->getContent(),
				)
			);
		}

		return array(
			'title' => tr('Create Workspace Template'),
			'id' => $id,
		);
	}

	function action_edit_template($input)
	{
		if (! Perms::get()->admin) {
			throw new Services_Exception_Denied;
		}

		global $prefs;

		$template = $this->utilities->getTemplate($input->id->int());
		if ($template['is_advanced'] == 'y') {
			return array('FORWARD' => array('action' => 'advanced_edit', 'id' => $input->id->int()));
		}

		if ($_SERVER['REQUEST_METHOD'] == 'POST') {
			$builder = new Services_Workspace_ProfileBuilder;

			if ($prefs['feature_areas'] == 'y' && $input->area->int()) {
				$builder->addObject(
					'area_binding',
					'binding',
					array(
						'category' => $builder->user('category'),
						'perspective' => $builder->user('perspective'),
					)
				);
			}

			foreach ($input->groups as $internal => $info) {
				$permissions = array_filter(preg_split('/\W+/', $info->permissions->none()));
				$builder->addGroup($internal, $info->name->text(), $info->autojoin->int() > 0);
				$builder->setPermissions($internal, 'category', $builder->user('category'), $permissions);
			}

			$builder->setManagingGroup($input->managingGroup->word());

			foreach ($input->pages as $page) {
				$builder->addObject(
					'wiki_page',
					uniqid(),
					array(
						'name' => $page->name->pagename(),
						'namespace' => $page->namespace->pagename(),
						'content' => $page->content->wikicontent(),
						'categories' => $builder->user('category'),
					)
				);
			}

			$this->utilities->replaceTemplate(
				$input->id->int(),
				array(
					'name' => $input->name->text(),
					'definition' => $builder->getContent(),
				)
			);
		}

		$template = $this->utilities->getTemplate($input->id->int());
		$profile = Tiki_Profile::fromString($template['definition']);
		$analyser = new Services_Workspace_ProfileAnalyser($profile);

		$hasArea = $analyser->contains(
			array(
				'type' => 'area_binding',
				'ref' => 'binding',
				'category' => $analyser->user('category'),
				'perspective' => $analyser->user('perspective'),
			)
		) ? 'y' : 'n';

		return array(
			'title' => tr('Edit template %0', $template['name']),
			'id' => $input->id->int(),
			'name' => $template['name'],
			'area' => ($prefs['feature_areas'] == 'y') ? $hasArea : null,
			'groups' => $analyser->getGroups('category', $analyser->user('category')),
			'pages' => $analyser->getObjects(
				'wiki_page',
				array(
					'name' => '{namespace}',
					'namespace' => null,
					'content' => '',
				)
			),
		);
	}

	function action_advanced_edit($input)
	{
		if (! Perms::get()->admin) {
			throw new Services_Exception_Denied;
		}

		if ($definition = $input->edit->wikicontent()) {
			$this->utilities->replaceTemplate(
				$input->id->int(),
				array(
					'name' => $input->name->text(),
					'definition' => $definition,
					'is_advanced' => 'y',
				)
			);
		}

		$template = $this->utilities->getTemplate($input->id->int());

		return array(
			'title' => tr('Edit template %0', $template['name']),
			'id' => $input->id->int(),
			'name' => $template['name'],
			'definition' => $template['definition'],
			'is_advanced' => $template['is_advanced'],
		);
	}

	function action_select_permissions($input)
	{
		if (! Perms::get()->admin) {
			throw new Services_Exception_Denied;
		}

		$permissions = array();

		if ($raw = $input->permissions->none()) {
			$permissions = array_map(
				function ($list)
				{
					$list = preg_split('/\W+/', $list);
					return array_filter($list);
				},
				$raw
			);
		} elseif ($checkboxes = $input->check->word()) {
			$permissions = $checkboxes;
		}

		$userlib = TikiLib::lib('user');

		$descriptions = array();
		$rawList = $userlib->get_permissions(0, -1, 'permName_asc', '', 'category', '', true);
		foreach ($rawList['data'] as $raw) {
			$type = $raw['type'];
			if (! isset($descriptions[$type])) {
				$descriptions[$type] = array();
			}

			$descriptions[$type][] = $raw;
		}

		return array(
			'groups' => array_keys($permissions),
			'permissions' => $permissions,
			'descriptions' => $descriptions,
		);
	}

	function action_edit_content($input)
	{
		$text = $input->content->text();
		$content = $input->content->wikicontent();

		$prefix = 'wikicontent:';
		if ($page = $input->page->pagename()) {
			$content = null;
		} elseif (substr($text, 0, strlen($prefix)) === $prefix) {
			$page = substr($text, strlen($prefix));
			$content = null;
		}

		return array(
			'page' => $page,
			'content' => $content,
		);
	}
	
}

