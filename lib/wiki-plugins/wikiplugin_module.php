<?php
// (c) Copyright 2002-2013 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function wikiplugin_module_info()
{
	global $lang;

	$modlib = TikiLib::lib('mod');
	$cachelib = TikiLib::lib('cache');

	if (! $modules_options = $cachelib->getSerialized('module_list_for_plugin' . $lang)) {
		$all_modules = $modlib->get_all_modules();
		$all_modules_info = array_combine($all_modules, array_map(array( $modlib, 'get_module_info' ), $all_modules));
		uasort($all_modules_info, 'compare_names');
		$modules_options = array();
		foreach ($all_modules_info as $module => $module_info) {
			$modules_options[] = array('text' => $module_info['name'] . ' (' . $module . ')', 'value' => $module);
		}

		$cachelib->cacheItem('module_list_for_plugin' . $lang, serialize($modules_options));
	}

	return array(
		'name' => tra('Insert Module'),
		'documentation' => 'PluginModule',
		'description' => tra('Display a module'),
		'prefs' => array( 'wikiplugin_module' ),
		'validate' => 'all',
		'format' => 'html',
		'icon' => 'img/icons/module.png',
		'extraparams' =>true,
		'tags' => array( 'basic' ),
		'params' => array(
			'module' => array(
				'required' => true,
				'name' => tra('Module Name'),
				'description' => tra('Module name as known in Tiki'),
				'default' => '',
				'options' => $modules_options,
			),
			'notitle' =>array(
				'required' => false,
				'name' => tra('No Title'),
				'description' => tra('Select Yes (y) to hide the title (default is to show the title)'),
				'options' => array(
					array('text' => '', 'value' => ''), 
					array('text' => tra('Yes'), 'value' => 'y'), 
					array('text' => tra('No'), 'value' => 'n'),
				),
				'advanced' => true,
			),
			'title' => array(
				'name' => tra('Module Title'),
				'description' => tra('Title to display at the top of the box, assuming No Title is not set to Yes (y).'),
				'filter' => 'striptags',
				'advanced' => true,
			),
			'float' => array(
				'required' => false,
				'name' => tra('Float'),
				'description' => tra('Align the module to the left or right on the page allowing other elements to align against it'),
				'default' => '',
				'advanced' => true,
				'options' => array(
					array('text' => '', 'value' => ''), 
					array('text' => 'No Float', 'value' => 'nofloat'), 
					array('text' => tra('Left'), 'value' => 'left'), 
					array('text' => tra('Right'), 'value' => 'right')
				)
			),
			'max' => array(
				'required' => false,
				'name' => tra('Max'),
				'description' => tra('Number of rows (default: 10)'),
				'default' => 10,
				'advanced' => true,
			),
			'np' => array(
				'required' => false,
				'name' => tra('Parse'),
				'description' => tra('Parse wiki syntax.') . ' ' . tra('Default:') . ' ' . tra('No'),
				'default' => '1',
				'options' => array(
					array('text' => '', 'value' => ''), 
					array('text' => tra('Yes'), 'value' => '0'), 
					array('text' => tra('No'), 'value' => '1'), 
				),
				'advanced' => true,
			),
			'nobox' => array(
				'name' => tra('No Box'),
				'description' => 'y|n '.tra('Show only the content with no box surrounding it.'),
				'section' => 'appearance',
				'filter' => 'alpha',
				'advanced' => true,
				'options' => array(
					array('text' => '', 'value' => ''), 
					array('text' => tra('Yes'), 'value' => 'y'), 
					array('text' => tra('No'), 'value' => 'n')
				)
			),
			'decoration' => array(
				'required' => false,
				'name' => tra('Decoration'),
				'description' => tra('Show box decorations (default is to show them)'),
				'advanced' => true,
				'options' => array(
					array('text' => '', 'value' => ''), 
					array('text' => tra('Yes'), 'value' => '1'), 
					array('text' => tra('No'), 'value' => '0'), 
				)
			),
			'flip' => array(
				'required' => false,
				'name' => tra('Flip'),
				'description' => tra('Add ability to show/hide the content of the module (default is the site admin setting for modules)'),
				'section' => 'appearance',
				'options' => array(
					array('text' => '', 'value' => ''), 
					array('text' => tra('Yes'), 'value' => '1'), 
					array('text' => tra('No'), 'value' => '0'), 
				),
				'advanced' => true,
			),
			'bgcolor' => array(
				'required' => false,
				'name' => tra('Title Background'),
				'description' => tra('Override the background color for the title (if the title is shown). The value can be a color name (ex: bgcolor=blue) or a hexadecimal value (ex: bgcolor=#FFEBCD)'),
				'default' => '',
				'filter' => 'striptags',
				'advanced' => true,
			),
			'module_style' => array(
				'required' => false,
				'name' => tra('Module Style'),
				'description' => tra('Inline CSS for the containing DIV element, e.g. "max-width:80%"'),
				'filter' => 'striptags',
				'default' => '',
				'advanced' => true,
			),
			'style' => array(
				'name' => tra('Style'),
				'description' => tra('CSS styling for the module data itself.'),
				'filter' => 'striptags',
				'section' => 'appearance',
				'advanced' => true,
			),
			'topclass' => array(
				'name' => tra('Containing Class'),
				'description' => tra('Custom CSS class around.'),
				'filter' => 'striptags',
				'section' => 'appearance',
				'advanced' => true,
			),
			'class' => array(
				'name' => tra('Class'),
				'description' => tra('Custom CSS class.'),
				'section' => 'appearance',
				'filter' => 'striptags',
				'advanced' => true,
			),
			'category' => array(
				'name' => tra('Category'),
				'description' => tra('Module displayed depending on category. Multiple category ids or names can be separated by semi-colons.'),
				'section' => 'visibility',
				'separator' => ';',
				'filter' => 'alnum',
				'advanced' => true,
			),
			'nocategory' => array(
				'name' => tra('No Category'),
				'description' => tra('Module hidden depending on category. Multiple category ids or names can be separated by semi-colons. This takes precedence over the category parameter above.'),
				'section' => 'visibility',
				'separator' => ';',
				'filter' => 'alnum',
				'advanced' => true,
			),
			'perspective' => array(
				'name' => tra('Perspective'),
				'description' => tra('Only display the module if in one of the listed perspective IDs. Semi-colon separated.'),
				'separator' => ';',
				'filter' => 'digits',
				'section' => 'visibility',
				'advanced' => true,
			),
			'lang' => array(
				'name' => tra('Language'),
				'description' => tra('Module only applicable for the specified languages. Languages are defined as two character language codes. Multiple values can be separated by semi-colons.'),
				'separator' => ';',
				'filter' => 'lang',
				'section' => 'visibility',
				'advanced' => true,
			),
			'section' => array(
				'name' => tra('Section'),
				'description' => tra('Module only applicable for the specified sections. Multiple values can be separated by semi-colons.'),
				'separator' => ';',
				'filter' => 'striptags',
				'section' => 'visibility',
				'advanced' => true,
			),
			'page' => array(
				'name' => tra('Page Filter'),
				'description' => tra('Module only applicable on the specified page names. Multiple values can be separated by semi-colons.'),
				'separator' => ';',
				'filter' => 'pagename',
				'section' => 'visibility',
				'advanced' => true,
			),
			'nopage' => array(
				'name' => tra('No Page'),
				'description' => tra('Module not applicable on the specified page names. Multiple values can be separated by semi-colons.'),
				'separator' => ';',
				'filter' => 'pagename',
				'section' => 'visibility',
				'advanced' => true,
			),
			'theme' => array(
				'name' => tra('Theme'),
				'description' => tra('Module enabled or disabled depending on the theme file name (e.g. "thenews.css"). Specified themes can be either included or excluded. Theme names prefixed by "!" are in the exclusion list. Multiple values can be separated by semi-colons.'),
				'separator' => ';',
				'filter' => 'themename',
				'section' => 'visibility',
				'advanced' => true,
			),
			'creator' => array(
				'name' => tra('Creator'),
				'description' => tra('Module only available based on the relationship of the user with the wiki page. Either only creators (y) or only non-creators (n) will see the module.'),
				'filter' => 'alpha',
				'section' => 'visibility',
				'advanced' => true,
			),
			'contributor' => array(
				'name' => tra('Contributor'),
				'description' => tra('Module only available based on the relationship of the user with the wiki page. Either only contributors (y) or only non-contributors (n) will see the module.'),
				'filter' => 'alpha',
				'section' => 'visibility',
				'advanced' => true,
			),
		)
	);
}

function wikiplugin_module($data, $params)
{
	static $instance = 0;

	$out = '';
	
	extract($params, EXTR_SKIP);

	if (!isset($float)) {
		$float = 'nofloat';
	}

    if (!isset($max)) {
        if (!isset($rows)) {
            $max = 10; // default value
        } else $max=$rows; // rows=> used instead of max=> ?
    }

	if (!isset($np)) {
		$np = '1';
	}

	if (!isset($module) or !$module) {
		$out = '<form class="box" id="modulebox">';

		$out .= '<br /><select name="choose">';
		$out .= '<option value="">' . tra('Please choose a module'). '</option>';
		$out .= '<option value="" style="background-color:#bebebe;">' . tra('to be used as argument'). '</option>';
		$out .= '<option value="" style="background-color:#bebebe;">{MODULE(module=>name_of_module)}</option>';
		$handle = opendir('modules');

		while ($file = readdir($handle)) {
			if ((substr($file, 0, 4) == "mod-") and (substr($file, -4, 4) == ".php")) {
				$mod = substr(substr(basename($file), 4), 0, -4);

				$out .= "<option value=\"$mod\">$mod</option>";
			}
		}

		$out .= '</select></form>';
	} else {

		$instance++;
		if (empty($moduleId)) {
			$moduleId = 'wikiplugin_' . $instance;
		}

		$module_reference = array(
			'moduleId' => $moduleId,
			'name' => $module,
			'params' => $params,
			'rows' => $max,
			'position' => null,
			'ord' => null,
			'cache_time'=> 0,
		);

		if (!empty($module_style)) {
			$module_reference['module_style'] = $module_style;
		}

		global $modlib; require_once 'lib/modules/modlib.php';
		$out = $modlib->execute_module($module_reference);
	}

	if ($out) {
		if ($float != 'nofloat') {
			$data = "<div style='float: $float;'>$out</div>";
		} else {
			$data = "<div>$out</div>";
		}
	} else {
		// Display error message
		$data = "<div class=\"highlight\">" . tra("Sorry, no such module"). "<br /><b>$module</b></div>" . $data;
	}

	return $data;
}
