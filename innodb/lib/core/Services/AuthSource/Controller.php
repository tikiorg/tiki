<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Services_AuthSource_Controller
{
	function setUp()
	{
		if (! Perms::get()->admin) {
			throw new Services_Exception(tr('Permission Denied'), 403);
		}
	}

	private function sources()
	{
		return TikiDb::get()->table('tiki_source_auth');
	}

	function action_list($input)
	{
		return $this->sources()->fetchColumn('identifier', array());
	}

	function action_save($input)
	{
		$url = $input->url->url();
		$info = parse_url($url);

		$identifier = $input->identifier->text();
		$method = $input->method->alpha();
		$arguments = $input->arguments->none();

		if (! $info || ! $identifier || ! $method || ! $arguments) {
			throw new Services_Exception(tr('Invalid data'), 406);
		}

		return $this->sources()->insertOrUpdate(array(
			'scheme' => $info['scheme'],
			'domain' => $info['host'],
			'path' => $info['path'],
			'method' => $method,
			'arguments' => json_encode($arguments),
		), array(
			'identifier' => $identifier,
		));
	}

	function action_fetch($input)
	{
		$data = $this->sources()->fetchFullRow(array(
			'identifier' => $input->identifier->text(),
		));

		$data['arguments'] = json_decode($data['arguments'], true);
		$data['url'] = "{$data['scheme']}://{$data['domain']}{$data['path']}";

		return $data;
	}

	function action_delete($input)
	{
		return $this->sources()->delete(array(
			'identifier' => $input->identifier->text(),
		));
	}
}

