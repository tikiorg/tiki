<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
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
		'iconname' => 'module',
		'introduced' => 1,
		'extraparams' =>true,
		'tags' => array( 'basic' ),
		'params' => array(
			'module' => array(
				'required' => true,
				'name' => tra('Module Name'),
				'description' => tra('Module name as known in Tiki'),
				'since' => '1',
				'default' => '',
				'filter' => 'text',
				'options' => $modules_options,
			),
			'notitle' =>array(
				'required' => false,
				'name' => tra('No Title'),
				'description' => tr('Select Yes (%0y%1) to hide the title (default is to show the title)', '<code>', '</code>'),
				'since' => '3.0',
				'options' => array(
					array('text' => '', 'value' => ''), 
					array('text' => tra('Yes'), 'value' => 'y'), 
					array('text' => tra('No'), 'value' => 'n'),
				),
				'filter' => 'alpha',
				'advanced' => true,
			),
			'title' => array(
				'name' => tra('Module Title'),
				'description' => tr('Title to display at the top of the box, assuming No Title is not set to Yes (%0y%1).',
					'<code>', '</code>'),
				'since' => '1',
				'filter' => 'text',
				'advanced' => true,
			),
			'float' => array(
				'required' => false,
				'name' => tra('Float'),
				'description' => tra('Align the module to the left or right on the page allowing other elements to align against it'),
				'since' => '1',
				'default' => '',
				'filter' => 'word',
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
				'description' => tr('Number of rows (default: %010%1)', '<code>', '</code>'),
				'since' => '1',
				'default' => 10,
				'filter' => 'digits',
				'advanced' => true,
			),
			'np' => array(
				'required' => false,
				'name' => tra('Parse'),
				'description' => tra('Parse wiki syntax.') . ' ' . tra('Default:') . ' ' . tra('No'),
				'since' => '1',
				'default' => '1',
				'filter' => 'digits',
				'options' => array(
					array('text' => '', 'value' => ''), 
					array('text' => tra('Yes'), 'value' => '0'), 
					array('text' => tra('No'), 'value' => '1'), 
				),
				'advanced' => true,
			),
			'nobox' => array(
				'name' => tra('No Box'),
				'description' => 'y|n '.tra('Show only the content with no title or borders, etc. around the content.'),
				'since' => '9.0',
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
				'name' => tra('Title, background, etc'),
				'description' => tra('Show module title (heading) background, etc. (default is to show them)'),
				'since' => '1',
				'advanced' => true,
				'filter' => 'digits',
				'options' => array(
					array('text' => '', 'value' => ''), 
					array('text' => tra('Yes'), 'value' => '1'), 
					array('text' => tra('No'), 'value' => '0'), 
				)
			),
			'flip' => array(
				'required' => false,
				'name' => tra('Flip'),
				'description' => tra('Add ability to show/hide the content of the module (default is the site admin
					setting for modules)'),
				'since' => '1',
				'section' => 'appearance',
				'filter' => 'digits',
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
				'description' => tr('Override the background color for the title (if the title is shown). The value
					can be a color name (ex: %0bgcolor="blue"%1) or a hexadecimal value (ex: %0bgcolor="#FFEBCD"%1)',
					'<code>', '</code>'),
				'since' => '9.0',
				'default' => '',
				'filter' => 'text',
				'advanced' => true,
			),
			'module_style' => array(
				'required' => false,
				'name' => tra('Module Style'),
				'description' => tr('Inline CSS for the containing div element, for example, %0max-width:80%%1',
					'<code>', '</code>'),
				'since' => '9.0',
				'filter' => 'text',
				'accepted' => tra('Valid CSS styling'),
				'default' => '',
				'advanced' => true,
			),
			'style' => array(
				'name' => tra('Style'),
				'description' => tra('CSS styling for the module data itself.'),
				'since' => '9.0',
				'filter' => 'text',
				'section' => 'appearance',
				'accepted' => tra('Valid CSS styling'),
				'advanced' => true,
			),
			'topclass' => array(
				'name' => tra('Containing Class'),
				'description' => tra('Custom CSS class of div around the module.'),
				'since' => '9.0',
				'filter' => 'text',
				'section' => 'appearance',
				'accepted' => tra('Valid CSS class'),
				'advanced' => true,
			),
			'class' => array(
				'name' => tra('Class'),
				'description' => tra('Custom CSS class.'),
				'since' => '9.0',
				'section' => 'appearance',
				'filter' => 'text',
				'accepted' => tra('Valid CSS class'),
				'advanced' => true,
			),
			'category' => array(
				'name' => tra('Category'),
				'description' => tra('Module displayed depending on category. Multiple category ids or names can be
					separated by semi-colons.'),
				'since' => '9.0',
				'section' => 'visibility',
				'separator' => ';',
				'filter' => 'alnum',
				'advanced' => true,
			),
			'nocategory' => array(
				'name' => tra('No Category'),
				'description' => tra('Module hidden depending on category. Multiple category ids or names can be
					separated by semi-colons. This takes precedence over the category parameter above.'),
				'since' => '9.0',
				'section' => 'visibility',
				'separator' => ';',
				'filter' => 'alnum',
				'advanced' => true,
			),
			'perspective' => array(
				'name' => tra('Perspective'),
				'description' => tra('Only display the module if in one of the listed perspective IDs. Semi-colon
					separated.'),
				'since' => '9.0',
				'separator' => ';',
				'filter' => 'digits',
				'section' => 'visibility',
				'advanced' => true,
			),
			'lang' => array(
				'name' => tra('Language'),
				'description' => tra('Module only applicable for the specified languages. Languages are defined as two
					character language codes. Multiple values can be separated by semi-colons.'),
				'since' => '9.0',
				'separator' => ';',
				'filter' => 'lang',
				'section' => 'visibility',
				'advanced' => true,
			),
			'section' => array(
				'name' => tra('Section'),
				'description' => tra('Module only applicable for the specified sections. Multiple values can be
					separated by semi-colons.'),
				'since' => '9.0',
				'separator' => ';',
				'filter' => 'text',
				'section' => 'visibility',
				'advanced' => true,
			),
			'page' => array(
				'name' => tra('Page Filter'),
				'description' => tra('Module only applicable on the specified page names. Multiple values can be
					separated by semi-colons.'),
				'since' => '9.0',
				'separator' => ';',
				'filter' => 'pagename',
				'section' => 'visibility',
				'advanced' => true,
			),
			'nopage' => array(
				'name' => tra('No Page'),
				'description' => tra('Module not applicable on the specified page names. Multiple values can be
					separated by semi-colons.'),
				'since' => '9.0',
				'separator' => ';',
				'filter' => 'pagename',
				'section' => 'visibility',
				'advanced' => true,
			),
			'theme' => array(
				'name' => tra('Theme'),
				'description' => tr('Module enabled or disabled depending on the theme file name (e.g.
					%0thenews.css%1). Specified themes can be either included or excluded. Theme names prefixed by %0!%1
					are in the exclusion list. Multiple values can be separated by semi-colons.', '<code>', '</code>'),
				'since' => '9.0',
				'separator' => ';',
				'filter' => 'themename',
				'section' => 'visibility',
				'advanced' => true,
			),
			'creator' => array(
				'name' => tra('Creator'),
				'description' => tr('Module only available based on the relationship of the user with the wiki page.
					Either only creators (%0y%1) or only non-creators (%0n%1) will see the module.', '<code>', '</code>'),
				'since' => '9.0',
				'filter' => 'alpha',
				'section' => 'visibility',
				'advanced' => true,
			),
			'contributor' => array(
				'name' => tra('Contributor'),
				'description' => tra('Module only available based on the relationship of the user with the wiki page.
					Either only contributors (%0y%1) or only non-contributors (%0n%1) will see the module.', '<code>',
					'</code>'),
				'since' => '9.0',
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
			'position' => '_wp_',
			'ord' => $instance,
			'cache_time'=> 0,
		);

		if (!empty($module_style)) {
			$module_reference['module_style'] = $module_style;
		}

		$modlib = TikiLib::lib('mod');
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

	if ($module == 'register') {
        // module register (maybe others too?) adds ~np~ to plugin output so remove them
        $data = preg_replace('/~[\/]?np~/ms', '', $data);
	}
	return $data;
}
