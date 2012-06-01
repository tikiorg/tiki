<?php
// (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Services_JisonParser_WikiPlugin
{
	function action_pluginbody($input)
	{
		global $tikilib;

		$key = $input->key->text();
		$page = $input->page->text();

		$info = $tikilib->get_page_info($page);
		$perms = $tikilib->get_perm_object($page, 'wiki page', $info, true);
		if ($perms['tiki_p_edit'] !== 'y') return array();

		$parser = new JisonParser_Wiki_PluginAjaxHandler();
		$data = TikiLib::lib('tiki')->getOne('SELECT data FROM tiki_pages WHERE pageName = ?', $page);

		$parser->parse($data);

		return array(
			'body' => JisonParser_Wiki_PluginAjaxHandler::$plugins[$key]
		);
	}
}