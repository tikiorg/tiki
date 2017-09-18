<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$


class Services_StyleGuide_Controller
{
	function setUp()
	{
		Services_Exception_Disabled::check('theme_styleguide');
	}

	/**
	 * Display the style guide tool
	 *
	 * @param JitFilter $input
	 *
	 * @return array
	 */
	function action_show($input)
	{

		$sections =$input->sections->text();

		if (empty($sections)) {
			$sections = [
				'alerts',
				'buttons',
				'colors',
				'dropdowns',
				'fonts',
				'forms',
				'icons',
				'headings',
				'lists',
				'navbars',
				'tables',
				'tabs',
			];
		} else {
			$sections =  explode(',', $sections);
		}

		TikiLib::lib('header')
			->add_jsfile('vendor_bundled/vendor/itsjavi/bootstrap-colorpicker/dist/js/bootstrap-colorpicker.js')
			->add_cssfile('vendor_bundled/vendor/itsjavi/bootstrap-colorpicker/dist/css/bootstrap-colorpicker.css')
			->add_cssfile('themes/base_files/css/style-guide.css')
			->add_jsfile('lib/jquery_tiki/style-guide.js')
		;

		return [
			'title' => tr('Style Guide'),
			'sections' => $sections,
		];
	}
}