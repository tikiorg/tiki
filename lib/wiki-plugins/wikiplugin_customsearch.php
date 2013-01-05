<?php
// (c) Copyright 2002-2013 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function wikiplugin_customsearch_info()
{
	return array(
		'name' => tra('Custom Search'),
		'documentation' => 'PluginCustomSearch',
		'description' => tra('Custom Search Interface that displays results using the LIST plugin'),
		'prefs' => array('wikiplugin_customsearch', 'wikiplugin_list', 'feature_ajax'),
		'body' => tra('LIST plugin configuration information'),
		'filter' => 'wikicontent',
		'icon' => 'img/icons/text_list_bullets.png',
		'tags' => array('advanced'),
		'params' => array(
			'wiki' => array(
				'required' => true,
				'name' => tra('Template wiki page'),
				'description' => tra('Wiki page where search user interface template is found'),
				'filter' => 'pagename',
				'default' => '',
			),
			'id' => array(
				'required' => false,
				'name' => tra('Alphanumeric Unique Identifier for search'),
				'description' => tra('A unique identifier to distinguish custom searches for storing of previous search criteria entered by users'),
				'filter' => 'alnum',
				'default' => '0',
			),
			'autosearchdelay' => array(
				'required' => false,
				'name' => tra('Autotrigger AJAX search on criteria change'),
				'description' => tra('Delay in milliseconds before automatically triggering search after change (0 disables)'),
				'filter' => 'digits',
				'default' => '0',
			),
			'searchfadediv' => array(
				'required' => false,
				'name' => tra('Div to fade when AJAX search in progress'),
				'description' => tra('The specific ID of the specific div to fade out when AJAX search is in progress, if not set will attempt to fade the whole area or if failing simply show the spinner'),
				'filter' => 'text',
				'default' => '',
			),
			'recalllastsearch' => array(
				'required' => false,
				'name' => tra('Return users to same search parameters on coming back to the search page after leaving'),
				'description' => tra('In the same session, return users to same search parameters on coming back to the search page after leaving'),
				'options' => array(
					array('text' => tra('Yes'), 'value' => '1'),
					array('text' => tra('No'), 'value' => '0'),
				),
				'filter' => 'digits',
				'default' => '0',
			),
			'callbackscript' => array(
				'required' => false,
				'name' => tra('Custom JavaScript wiki page'),
				'description' => tra('The wiki page on which custom JavaScript that is to be executed on return of AJAX results'),
				'filter' => 'pagename',
				'default' => '',
			),
			'destdiv' => array(
				'required' => false,
				'name' => tra('Destination Div'),
				'description' => tra('Id of a pre-existing div to contain the search results'),
				'filter' => 'text',
				'default' => '',
			),
			'searchonload' => array(
				'required' => false,
				'name' => tra('Search On Load'),
				'description' => tra('Execute the search when the page loads (default: Yes)'),
				'options' => array(
					array('text' => tra('Yes'), 'value' => '1'),
					array('text' => tra('No'), 'value' => '0'),
				),
				'filter' => 'digits',
				'default' => '1',
			),
		),
	);
}

function wikiplugin_customsearch($data, $params)
{
	global $prefs;
	if (!isset($params['wiki'])) {
		return tra('Template is not specified');
	} elseif (!TikiLib::lib('tiki')->page_exists($params['wiki'])) {
		return tra('Template page not found');
	}
	if (isset($params['id'])) {
		$id = $params['id'];
	} else {
		$id = '0';
	}
	if (isset($params['recalllastsearch']) && $params['recalllastsearch'] == 1 && (!isset($_REQUEST['forgetlastsearch']) || $_REQUEST['forgetlastsearch'] != 'y')) {
		$recalllastsearch = 1;
	} else {
		$recalllastsearch = 0;
	}
	if (isset($params['autosearchdelay'])) {
		$autosearchdelay = $params['autosearchdelay'];
	} else {
		$autosearchdelay = 0;
	}
	if (isset($params['searchfadediv'])) {
		$searchfadediv = $params['searchfadediv'];
	} else {
		$searchfadediv = '';
	}
	if (!isset($_REQUEST["offset"])) {
		$offset = 0;
	} else {
		$offset = $_REQUEST["offset"];
	}
	if (isset($_REQUEST['maxRecords'])) {
		$maxRecords = $_REQUEST['maxRecords'];
	} elseif ($recalllastsearch && !empty($_SESSION["customsearch_$id"]['maxRecords'])) {
		$maxRecords = $_SESSION["customsearch_$id"]['maxRecords'];
	} else {
		$maxRecords = $prefs['maxRecords'];
	}
	if (!empty($_REQUEST['sort_mode'])) {
		$sort_mode = $_REQUEST['sort_mode'];
	} elseif ($recalllastsearch && !empty($_SESSION["customsearch_$id"]['sort_mode'])) {
		$sort_mode = $_SESSION["customsearch_$id"]['sort_mode'];
	} else {
		$sort_mode = '';
	}
	if (!isset($params['searchonload'])) {
		$params['searchonload'] = 1;
	}

	$wikitpl = "tplwiki:" . $params['wiki'];
	$wikicontent = TikiLib::lib('smarty')->fetch($wikitpl);
	TikiLib::lib('parser')->parse_wiki_argvariable($wikicontent);

	$matches = WikiParser_PluginMatcher::match($wikicontent);

	$parser = new WikiParser_PluginArgumentParser;
	$fingerprint = md5(print_r($matches, true));
	$sessionprint = "customsearch_{$id}_$fingerprint";
	if (isset($_SESSION[$sessionprint]) && $_SESSION[$sessionprint] != $fingerprint) {
		unset($_SESSION["customsearch_$id"]);
	}
	$_SESSION[$sessionprint] = $fingerprint;

	// important that offset from session is set after fingerprint check otherwise blank page might show
	if ($recalllastsearch && !isset($_REQUEST['offset']) && !empty($_SESSION["customsearch_$id"]["offset"])) {
		$offset = $_SESSION["customsearch_$id"]["offset"];
	}

	$groups = array();
	$textrangegroups = array();
	$daterangegroups = array();

	$script = "
function add_customsearch_$id(fieldid, filter) {
	customsearch_{$id}_searchdata[fieldid] = filter;
}
function remove_customsearch_$id(fieldid) {
	delete customsearch_{$id}_searchdata[fieldid];
}
customsearch_{$id}_searchdata = new Object();
customsearch_{$id}_basedata = " . json_encode((string) $data) . ";
$('#customsearch_$id').click(function() {
	// reset offset on reclick of form since new search should always start from 0 offset
	customsearch_offset_$id = 0;
});
$('#customsearch_$id').submit(function() {
	load_customsearch_$id($.toJSON(customsearch_{$id}_searchdata));
	return false;
});
";

	foreach ($matches as $match) {
		$name = $match->getName();
		$arguments = $parser->parse($match->getArguments());
		$key = $match->getInitialStart();
		$fieldid = "customsearch_{$id}_$key";
		if ($name == 'sort' && !empty($arguments['mode']) && empty($sort_mode)) {
			$sort_mode = $arguments['mode'];
			$match->replaceWith('');
			continue;
		}
		if ($arguments['_filter'] == 'content' && !empty($arguments['_field'])) {
			$filter = $arguments['_field'];
		} elseif ($arguments['_filter'] == 'content' && empty($arguments['_field'])) {
			$filter = 'content';
		} else {
			$filter = '';
		}
		if ( $filter && !empty($_REQUEST['default'][$filter]) ) {
			$default = $_REQUEST['default'][$filter];
		} elseif ($recalllastsearch && isset($_SESSION["customsearch_$id"][$fieldid])) {
			$default = $_SESSION["customsearch_$id"][$fieldid];
		} elseif (!empty($arguments['_default'])) {
			if (strpos($arguments['_default'], ',') !== false) {
				$default = explode(',', $arguments['_default']);
			} else {
				$default = $arguments['_default'];
			}
		} else {
			$default = '';
		}
		if ( $name == 'categories' ) {
			$parent = $arguments['_parent'];
			if (!empty($_REQUEST['defaultcat'][$parent])) {
				$default = $_REQUEST['defaultcat'][$parent];
			}
		}
		$function = "cs_design_{$name}";
		if (function_exists($function)) {
			if (isset($arguments['_group'])) {
				$groups[$fieldid] = $arguments['_group'];
				$fieldname = "customsearch_{$id}_gr" . $arguments['_group'];
			} elseif (isset($arguments['_textrange'])) {
				$textrangegroups[$fieldid] = $arguments['_textrange'];
				$fieldname = "customsearch_{$id}_textrange" . $arguments['_textrange'];
			} elseif (isset($arguments['_daterange'])) {
				$daterangegroups[$fieldid] = $arguments['_daterange'];
				$fieldname = "customsearch_{$id}_daterange" . $arguments['_daterange'];
			} else {
				$fieldname = $fieldid;
			}
			$match->replaceWith($function($id, $fieldname, $fieldid, $arguments, $default, $script, $groups, $autosearchdelay));
		}
	}

	if (!empty($params['callbackscript']) && TikiLib::lib('tiki')->page_exists($params['callbackscript'])) {
		$callbackscript_tpl = "wiki:" . $params['callbackscript'];
		$callbackScript = TikiLib::lib('smarty')->fetch($callbackscript_tpl);
	}

	$script .= "function load_customsearch_$id(searchdata) {\n";
	$searchfadetext = tr('Searching...');
	if ($searchfadediv) {
		$script .= "	if ($('#$searchfadediv').length) $('#$searchfadediv').modal('$searchfadetext');\n";
		$script .= "	else $('#customsearch_$id').modal('$searchfadetext');\n";
	} else {
		$script .= "	$('#customsearch_$id').modal('$searchfadetext');\n";
	}
	$script .= "
	var datamap = {
		basedata: customsearch_{$id}_basedata,
		adddata: searchdata,
		searchid: '$id',
		groups: '" . json_encode($groups) . "',
		textrangegroups: '" . json_encode($textrangegroups) . "',
		daterangegroups: '" . json_encode($daterangegroups) . "',
		offset: customsearch_offset_$id,
		maxRecords: customsearch_maxRecords_$id
	};
	if (customsearch_sort_mode_$id) {
		// blank sort_mode is not allowed by Tiki input filter
		datamap['sort_mode'] = customsearch_sort_mode_$id;
	}
	$.ajax({
		type: 'POST',
		url: $.service('tracker_search', 'customsearch'),
		data: datamap,
		dataType: 'html',
		success: function(data){
";

	if ($searchfadediv) {
		$script .= "			if ($('#$searchfadediv').length) $('#$searchfadediv').modal();\n";
		$script .= "			else $('#customsearch_$id').modal();\n";
	} else {
		$script .= "			$('#customsearch_$id').modal();\n";
	}
	if (!empty($params['destdiv'])) {
		$script .= "			$('#{$params['destdiv']}').html(data); customsearch_quiet_$id = false;\n";
	} else {
		$script .= "			$('#customsearch_{$id}_results').html(data); customsearch_quiet_$id = false;\n";
	}

	$script .= "			$(document).trigger('pageSearchReady');\n";
	if (!empty($callbackScript)) $script .= $callbackScript;
	$script .= "
		}
	});
};
customsearch_sort_mode_$id = '$sort_mode';
customsearch_offset_$id = $offset;
customsearch_maxRecords_$id = $maxRecords;
";

	if ($params['searchonload']) {
		$script .= "$('#customsearch_$id').submit();
";
	}
	TikiLib::lib('header')->add_jq_onready($script);

	$form = '<div id="' . "customsearch_$id" . '_form' . '"><form id="' . "customsearch_$id" . '">' . $matches->getText() . '</form></div>';

	if (empty($params['destdiv'])) {
		$results = '<div id="' . "customsearch_$id" . '_results"></div>';
	}

	$out = $form . $results;


	return $out;
}

function cs_design_setbasic(&$element, $fieldid, $fieldname, $arguments)
{
	$element->setAttribute('id', $fieldid);
	$element->setAttribute('name', $fieldname);
	foreach ($arguments as $k => $v) {
		if (substr($k, 0, 1) != '_') {
			$element->setAttribute($k, $v);
		}
	}
}

function cs_design_input($id, $fieldname, $fieldid, $arguments, $default, &$script, &$groups, $autosearchdelay = 0)
{
	$document = new DOMDocument;
	$element = $document->createElement('input');
	cs_design_setbasic($element, $fieldid, $fieldname, $arguments);
	extract($arguments, EXTR_SKIP);

	if ($type == 'checkbox' || $type == 'radio') {
		$val_selector = "$(this).is(':checked')";
	} else {
		$val_selector = "$(this).val()";
	}
	if ($type == 'radio') {
		$radioreset = "$('input[type=radio][name=$fieldname]').each(function() {
				remove_customsearch_$id($(this).attr('id'));
			});";
	} else {
		$radioreset = '';
	}

	$script .= "$('#$fieldid').change(function() {\n";
	if ($autosearchdelay) {
		$script .= "	if (typeof(customsearch_timeout_$id)!='undefined') clearTimeout(customsearch_timeout_$id);\n";
	}
	$script .= "	var filter = new Object();
	filter.config = " . json_encode($arguments) . ";
	filter.name = 'input';
	filter.value = $val_selector;
	$radioreset
	add_customsearch_$id('$fieldid', filter);
";
	if ($autosearchdelay) {
		$script .= "	if (!customsearch_quiet_$id)\n		customsearch_timeout_$id = setTimeout('$(\'#customsearch_$id\').submit()', $autosearchdelay);\n";
	}
	$script .= "});\n";

	if ($autosearchdelay) {
		// prevent enter from submitting form since the change itself will do so
		$script .= "$('#$fieldid').keydown(function(event) {
	if (event.keyCode == '13') {
		event.preventDefault();
		$('#$fieldid').trigger('change');
		return false;
	}
});
";
	}

	if ($default && $type != "hidden") {
		if ((string) $default != 'n' && ($type == 'checkbox' || $type == 'radio')) {
			$element->setAttribute('checked', 'checked');
		} else {
			$element->setAttribute('value', $default);
		}
		$script .= 	"customsearch_quiet_$id = true; $('#$fieldid').trigger('change');\ncustomsearch_quiet_$id = false;\n";
	} elseif ($type == "hidden") {
		$script .= 	"customsearch_quiet_$id = true; $('#$fieldid').trigger('change');\ncustomsearch_quiet_$id = false;\n";
	}

	$document->appendChild($element);
	return $document->saveHTML();
}

function cs_design_categories($id, $fieldname, $fieldid, $arguments, $default, &$script, &$groups, $autosearchdelay = 0)
{
	$document = new DOMDocument;
	extract($arguments, EXTR_SKIP);
	if (!isset($_style)) {
		$_style = 'select';
	}
	if (empty($_group) && ($_style == 'checkbox' || $_style == 'radio')) {
		return tr("_group is needed to be set if _style is checkbox or radio");
	}
	$showSubcategories = isset($_showdeep) && $_showdeep != 'n';
	if (isset($_parent) && ctype_digit($_parent) && $_parent > 0) {
		$filter = array('identifier'=>$_parent, 'type'=> $showSubcategories ? 'descendants' : 'children');
	} else {
		$filter = array('type'=>$showSubcategories ? 'all' : 'roots');
	}
	if (!isset($_categpath)) {
		$_categpath = false;
	}

	$cats = TikiLib::lib('categ')->getCategories($filter);

	$element = $document->createElement('select');
	cs_design_setbasic($element, $fieldid, $fieldname, $arguments);
	$document->appendChild($element);

	if ($_style == 'checkbox' || $_style == 'radio') {
		$currentlevel = 0;
		$orig_fieldid = $fieldid;
		foreach ($cats as $c) {
			$categId = $c['categId'];
			$fieldid = $orig_fieldid . "_cat$categId";
			$groups[$fieldid] = $arguments['_group']; // add new "subfield" to groups list
			$level = count($c['tepath']);
			if ($level > $currentlevel) {
				$ul{$level} = $document->createElement('ul');
				if ($currentlevel) {
					$ul{$currentlevel}->appendChild($ul{$level});
				} else {
					$document->appendChild($ul{$level});
				}
				$currentlevel = $level;
			} elseif ($level < $currentlevel) {
				$currentlevel = $level;
			}
			$li = $document->createElement('li');
			$ul{$currentlevel}->appendChild($li);
			$input = $document->createElement('input');
			$input->setAttribute('type', $_style);
			cs_design_setbasic($input, $fieldid, $fieldname, $arguments);
			$input->setAttribute('value', $categId);
			$li->appendChild($input);
			$label = $document->createTextNode($_categpath ? $c['relativePathString'] : $c['name']);
			$li->appendChild($label);

			if ($_style == 'radio') {
				$radioreset = "$('input[type=radio][name=$fieldname]').each(function() {
	remove_customsearch_$id($(this).attr('id'));
});"
;
			} else {
				$radioreset = '';
			}

			$script .= "$('#$fieldid').change(function() {";
			if ($autosearchdelay) {
				$script .= "	if (typeof(customsearch_timeout_$id)!='undefined') clearTimeout(customsearch_timeout_$id);\n";
			}
			$script .= "if ($(this).is(':checked')) {
	var filter = new Object();
	filter.config = " . json_encode($arguments) . ";
	filter.name = 'categories';
	filter.value = $(this).val();
	$radioreset
	add_customsearch_$id('$fieldid', filter);
} else {
	remove_customsearch_$id('$fieldid', filter);
}
";
			if ($autosearchdelay) {
				$script .= "	if (!customsearch_quiet_$id) customsearch_timeout_$id = setTimeout('$(\'#customsearch_$id\').submit()', $autosearchdelay);\n";
			}
			$script .= "});\n";

			if ($default && in_array($c['categId'], (array) $default)) {
				$element->setAttribute('checked', 'checked');
                		$script .= "customsearch_quiet_$id = true;
$('#$fieldid').trigger('change');
customsearch_quiet_$id = false;
";
			}
		}
	} elseif ($_style == 'select') {
		// leave a blank one in the front
		if (!isset($arguments['multiple']) && !isset($arguments['size']) || isset($arguments['_firstlabel'])) {
			if (!empty($arguments['_firstlabel'])) {
				$label = $arguments['_firstlabel'];
			} else {
				$label = '';
			}
			$option = $document->createElement('option', $label);
			$option->setAttribute('value', '');
			$element->appendChild($option);
		}
		$script .= "$('#$fieldid').change(function() {";
		if ($autosearchdelay) {
			$script .= "	if (typeof(customsearch_timeout_$id)!='undefined') clearTimeout(customsearch_timeout_$id);\n";
		}
		$script .= "	var filter = new Object();
	filter.config = " . json_encode($arguments) . ";
	filter.name = 'categories';
	filter.value = $(this).val();
	add_customsearch_$id('$fieldid', filter);
";
		if ($autosearchdelay) {
			$script .= "	if (!customsearch_quiet_$id) customsearch_timeout_$id = setTimeout('$(\'#customsearch_$id\').submit()', $autosearchdelay);\n";
		}
		$script .= "});";

		foreach ($cats as $c) {
			$option = $document->createElement('option', $_categpath ? $c['relativePathString'] : $c['name']);
			$option->setAttribute('value', $c['categId']);
			$element->appendChild($option);
			if ($default && in_array($c['categId'], (array) $default)) {
				$option->setAttribute('selected', 'selected');
				$script .= "customsearch_quiet_$id = true;
$('#$fieldid').trigger('change');
customsearch_quiet_$id = false;
";
			}
		}


	}

	return $document->saveHTML();
}

function cs_design_select($id, $fieldname, $fieldid, $arguments, $default, &$script, &$groups, $autosearchdelay = 0)
{
	$document = new DOMDocument;
	$element = $document->createElement('select');
	cs_design_setbasic($element, $fieldid, $fieldname, $arguments);
	$document->appendChild($element);

	if (isset($arguments['_labels'])) {
		$labels = explode(',', $arguments['_labels']);
	}
	if (isset($arguments['_options'])) {
		$options = explode(',', $arguments['_options']);
	} else {
		$options = array();
	}
	if (isset($arguments['_mandatory']) && $arguments['_mandatory'] == 'y') {
		$mandatory = true;
	} else {
		$mandatory = false;
	}
	// leave a blank one in the front
	if (!$mandatory && !isset($arguments['multiple']) && !isset($arguments['size']) || isset($arguments['_firstlabel'])) {
		if (!empty($arguments['_firstlabel'])) {
			$label = $arguments['_firstlabel'];
		} else {
			$label = '';
		}
		$option = $document->createElement('option', $label);
		$option->setAttribute('value', '');
		$element->appendChild($option);
	}

	$script .= "$('#$fieldid').change(function() {";
	if ($autosearchdelay) {
		$script .= "	if (typeof(customsearch_timeout_$id)!='undefined') clearTimeout(customsearch_timeout_$id);\n";
	}
	$script .= "	var filter = new Object();
	filter.config = " . json_encode($arguments) . ";
	filter.name = 'select';
	filter.value = $(this).val();
	add_customsearch_$id('$fieldid', filter);
";
	if ($autosearchdelay) {
		$script .= "	if (!customsearch_quiet_$id) customsearch_timeout_$id = setTimeout('$(\'#customsearch_$id\').submit()', $autosearchdelay);\n";
	}
	$script .= "});\n";

	foreach ($options as $k => $opt) {
		if (!empty($labels[$k])) {
			$body = $labels[$k];
		} else {
			$body = $opt;
		}
		$option = $document->createElement('option', $body);
		$option->setAttribute('value', $opt);
		if ($default && in_array($opt, (array) $default)) {
			$option->setAttribute('selected', 'selected');
			$script .= "	customsearch_quiet_$id = true;
$('#$fieldid').trigger('change');
customsearch_quiet_$id = false;
";
		}
		$element->appendChild($option);
	}
	return $document->saveHTML();
}

function cs_design_daterange($id, $fieldname, $fieldid, $arguments, $default, &$script, &$groups, $autosearchdelay = 0)
{
	extract($arguments, EXTR_SKIP);

	$smarty = TikiLib::lib('smarty');
	$smarty->loadPlugin('smarty_function_jscalendar');

	$params_from = array();
	$params_to = array();
	if (!empty($_showtime) && $_showtime == 'y') {
		$params_from['showtime'] = 'y';
		$params_to['showtime'] = 'y';
	} else {
		$params_from['showtime'] = 'n';
		$params_to['showtime'] = 'n';
	}
	$params_from['fieldname'] = $fieldname . '_from';
	$params_to['fieldname'] = $fieldname . '_to';
	$params_from['id'] = $fieldid_from = $fieldid . '_from';
	$params_to['id'] = $fieldid_to = $fieldid . '_to';

	if (!empty($_from)) {
		if ($_from == 'now') {
			$params_from['date'] = TikiLib::lib('tiki')->now;
		} else {
			$params_from['date'] = $_from;
		}
		if (empty($_to)) {
			if (empty($_gap)) {
				$_gap = 365 * 24 * 3600;
			}
			$params_to['date'] = $params_from['date'] + $_gap;
		}
	} else {
		$params_from['date'] = TikiLib::lib('tiki')->now;
	}
	if (!empty($_to)) {
		if ($_to == 'now') {
			$params_to['date'] = TikiLib::lib('tiki')->now;
		} else {
			$params_to['date'] = $_to;
		}
		if (empty($_from)) {
			if (empty($_gap)) {
				$_gap = 365 * 24 * 3600;
			}
			$params_from['date'] = $params_to['date'] - $_gap;
		}
	} elseif (empty($params_to['date'])) {
		$params_to['date'] = TikiLib::lib('tiki')->now + 365 * 24 * 3600;
	}

	$picker = '';
	$picker .= smarty_function_jscalendar($params_from, $smarty);
	$picker .= smarty_function_jscalendar($params_to, $smarty);

	$script .= "$('#{$fieldid_from}_dptxt,#{$fieldid_to}_dptxt').change(function() {";
	if ($autosearchdelay) {
		$script .= "if (typeof(customsearch_timeout_$id)!='undefined') clearTimeout(customsearch_timeout_$id);";
	}
	$script .= "var from = $('#$fieldid_from').val();";
	$script .= "var to = $('#$fieldid_to').val();";
	$script .= "from = from.substr(0,10);to = to.substr(0,10);"; // prevent trailing 000 from date picker
	$script .= "var filter = new Object();
		filter.config = " . json_encode($arguments) . ";
		filter.name = 'daterange';
		filter.value = from + ',' + to;
		add_customsearch_$id('$fieldid', filter);";
	if ($autosearchdelay) {
		$script .= "if (!customsearch_quiet_$id) customsearch_timeout_$id = setTimeout('$(\'#customsearch_$id\').submit()', $autosearchdelay);";
	}
	$script .= "});";

	return $picker;
}
