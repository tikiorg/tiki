<?php
// (c) Copyright 2002-2013 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

/**
 * Class Services_Edit_Controller
 *
 * Controller for various editing based services, wiki/html conversion, preview, inline editing etc
 *
 */
class Services_Edit_Controller {

	function setUp()
	{
		Services_Exception_Disabled::check('feature_wiki');
	}


	function action_towiki($input)
	{
		$res = TikiLib::lib('edit')->parseToWiki($input->data->none());

		return array(
			'data' => $res,
		);
	}

	function action_tohtml($input)
	{
		$res = TikiLib::lib('edit')->parseToWysiwyg($input->data->none(), false, $input->allowhtml->int() ? true : false);

		return array(
			'data' => $res,
		);
	}

	function action_inlinesave($input)
	{
		global $user;

		$pageName = $input->page->text();
		$info = TikiLib::lib('tiki')->get_page_info($pageName);
		$data = $input->data->none();

		// Check if HTML format is allowed
		if ($info['is_html']) {
			// Save as HTML
			$edit_data = TikiLib::lib('edit')->partialParseWysiwygToWiki($data);
			$is_html= '1';
		} else {
			// Convert HTML to wiki and save as wiki
			$edit_data = TikiLib::lib('edit')->parseToWiki($data);
			$is_html= null;
		}

		$edit_comment = tra('Inline editor update');
		$res = TikiLib::lib('tiki')->update_page($pageName, $edit_data, $edit_comment, $user,  $_SERVER['REMOTE_ADDR']);

		return array(
			'data' => $res,
		);
	}

	function action_preview($input) {

	}

}