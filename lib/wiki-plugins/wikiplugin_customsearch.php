<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
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
		'icon' => 'pics/icons/text_list_bullets.png',
		'tags' => array( 'new' ),		
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
				'name' => tra('Unique Alphanumeric ID for search'),
				'description' => tra('A unique ID to distinguish custom searches for storing of previous search criteria entered by users'),
				'filter' => 'text',
				'default' => '0',
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
		$id = preg_replace('/[^a-zA-Z0-9]/', '', $params['id']);
	} else {
		$id = '0';
	}
	if (!isset($_REQUEST["offset"])) {
		$offset = 0;
	} else {
		$offset = $_REQUEST["offset"];
	} 
	if (isset($_REQUEST['maxRecords'])) {
		$maxRecords = $_REQUEST['maxRecords'];
        } else {
		$maxRecords = $prefs['maxRecords']; 
	}		
	$wikitpl = "tplwiki:" . $params['wiki'];
	$wikicontent = TikiLib::lib('smarty')->fetch($wikitpl);

	$matches = WikiParser_PluginMatcher::match($wikicontent);

	$parser = new WikiParser_PluginArgumentParser;
	$fingerprint = md5(print_r($matches, true));
	$sessionprint = "customsearch_$id" . "_$fingerprint";
	if (isset($_SESSION[$sessionprint]) && $_SESSION[$sessionprint] != $fingerprint) {
		unset($_SESSION["customsearch_$id"]);
	} 
	$_SESSION[$sessionprint] = $fingerprint;
	
	$groups = array();
	$textrangegroups = array();
	$daterangegroups = array();

	$script .= "function add_customsearch_$id(fieldid, filter) {
			customsearch_$id" . "_searchdata[fieldid] = filter; 
		}
		function remove_customsearch_$id(fieldid) {
			delete customsearch_$id" . "_searchdata[fieldid];
		}
		customsearch_$id" . "_searchdata = new Object();
		customsearch_$id" . "_basedata = '" . json_encode((string) $data) . "';
		$('#customsearch_$id" . "').click(function() {
			// reset offset on reclick of submit button
			customsearch_offset_$id = 0;
		});
		$('#customsearch_$id" . "').submit(function() {
			load_customsearch_$id($.toJSON(customsearch_$id" . "_searchdata));
			return false;
		});";
		
	foreach ($matches as $k => $match) {
		$name = $match->getName(); 
		$arguments = $parser->parse($match->getArguments());
		$fieldid = "customsearch_$id" . "_$k";
		if (isset($_SESSION["customsearch_$id"][$fieldid])) {
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
		$function = "cs_design_{$name}";
		if (function_exists($function)) {
			if (isset($arguments['_group'])) {
				$groups[$fieldid] = $arguments['_group']; 
				$fieldname = "customsearch_$id" . "_gr" . $arguments['_group'];
			} elseif (isset($arguments['_textrange'])) {
				$textrangegroups[$fieldid] = $arguments['_textrange'];
				$fieldname = "customsearch_$id" . "_textrange" . $arguments['_textrange'];
			} elseif (isset($arguments['_daterange'])) {
				$daterangegroups[$fieldid] = $arguments['_daterange'];
				$fieldname = "customsearch_$id" . "_daterange" . $arguments['_daterange'];
			} else {
				$fieldname = $fieldid;
			}
			$match->replaceWith($function($id, $fieldname, $fieldid, $arguments, $default, $script, $groups));
		} 
	}

	$script .= "function load_customsearch_$id" . "(searchdata) { 
			$('#customsearch_$id" . "_results').modal('Searching...');	
			var datamap = {basedata: customsearch_$id" . "_basedata,
				adddata: searchdata,
				searchid: '$id',
				groups: '" . json_encode($groups) . "',
				textrangegroups: '" . json_encode($textrangegroups) . "',
				daterangegroups: '" . json_encode($daterangegroups) . "',
				offset: customsearch_offset_$id,
				maxRecords: customsearch_maxRecords_$id };	
			if (customsearch_sort_mode_$id) {
				// blank sort_mode is not allowed by Tiki input filter
				datamap[sort_mode] = customsearch_sort_mode_$id;
			}
			$.ajax({
				type: 'POST',
				url: 'customsearch_ajax.php',
				data: datamap, 
				dataType: 'html',
				success: function(data){
					$('#customsearch_$id" . "_results').html(data);
					$('#customsearch_$id" . "_results').modal();
				}
			});
		};
		customsearch_sort_mode_$id = '';
		customsearch_offset_$id = $offset;
		customsearch_maxRecords_$id = $maxRecords; 
		load_customsearch_$id('');";

	$form = '<div id="' . "customsearch_$id" . '_form' . '"><form id="' . "customsearch_$id" . '">' . $matches->getText() . '</form></div>'; 

	$results = '<div id="' . "customsearch_$id" . '_results"></div>';

	$out = '{JQ()}' . $script . '{JQ}' . $form . $results;;

	return $out;
}

function cs_design_setbasic(&$element, $fieldid, $fieldname, $arguments) {
	$element->setAttribute('id', $fieldid);
	$element->setAttribute('name', $fieldname);
	foreach($arguments as $k => $v) {
		if (substr($k,0,1) != '_') {
			$element->setAttribute($k, $v);
		}
	}		
}

function cs_design_input($id, $fieldname, $fieldid, $arguments, $default, &$script, &$groups) {
	$document = new DOMDocument;
	$element = $document->createElement('input');
	cs_design_setbasic($element, $fieldid, $fieldname, $arguments); 
	extract ($arguments, EXTR_SKIP);
	
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

	$script .= "$('#$fieldid').change(function() {
			var filter = new Object();
			filter.config = " . json_encode($arguments) . ";
			filter.name = 'input';
			filter.value = $val_selector;
			$radioreset
			add_customsearch_$id('$fieldid', filter); 
		});";

	if ($default) {
		if ($default != 'n' && ($type == 'checkbox' || $type == 'radio')) {
			$element->setAttribute('checked', 'checked');
		} else {
			$element->setAttribute('value', $default);
		}
		$script .= 	"$('#$fieldid').trigger('change');";
	} elseif ($type == "hidden") {
		$script .= 	"$('#$fieldid').trigger('change');";
	}
	
	$document->appendChild($element);
	return $document->saveHTML();
}

function cs_design_categories($id, $fieldname, $fieldid, $arguments, $default, &$script, &$groups) {
	$document = new DOMDocument;
	extract ($arguments, EXTR_SKIP);
	if (empty($_group) && ($_style == 'checkbox' || $_style == 'radio')) {
		return tr("_group is needed to be set if _style is checkbox or radio");
	}
	if (!isset($_style)) {
		$_style = 'select';
	}
	if (!isset($_parent)) {
		$_parent = 0;
	}		
	if (!isset($_categpath)) {
		$_categpath = false;
	}

	$cats = TikiLib::lib('categ')->get_viewable_child_categories($_parent, isset($_showdeep) && $_showdeep != 'n');

	if ($_style == 'checkbox' || $_style == 'radio') {
		$currentlevel = 0;
		$orig_fieldid = $fieldid;
		foreach ($cats as $c) {
			$fieldid = $orig_fieldid . "_cat$categId";
			$groups[$fieldid] = $arguments['_group']; // add new "subfield" to groups list
			$categId = $c['categId'];
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
			$label = $document->createTextNode($_categpath ? $c['categpath'] : $c['name']);
			$li->appendChild($label); 
			
			if ($_style == 'radio') {
				$radioreset = "$('input[type=radio][name=$fieldname]').each(function() {
							remove_customsearch_$id($(this).attr('id'));
						});";
			} else {
				$radioreset = '';	
			}

			$script .= "$('#$fieldid').change(function() {
				if ($(this).is(':checked')) {
					var filter = new Object();
					filter.config = " . json_encode($arguments) . ";
					filter.name = 'categories';
					filter.value = $(this).val();
					$radioreset
					add_customsearch_$id('$fieldid', filter); 
				} else {
					remove_customsearch_$id('$fieldid', filter);
				}
			});";

			if ($default && in_array($c, (array) $default)) {
				$element->setAttribute('checked', 'checked');
                		$script .= "$('#$fieldid').trigger('change');";
			} 
		} 
	} elseif ($_style == 'select') {
		$element = $document->createElement('select');
		cs_design_setbasic($element, $fieldid, $fieldname, $arguments);
		$document->appendChild($element);

		// leave a blank one in the front
		if (!isset($arguments['multiple']) && !isset($arguments['size']) || isset($arguments['_firstlabel'])) {
			if (!empty($arguments['_firstlabel'])) {
				$label = $arguments['_firstlabel'];
			} else {
				$label = '';
			}
			$option = $document->createElement('option', $label);
			$element->appendChild($option);
		} 
		$script .= "$('#$fieldid').change(function() { 
			var filter = new Object();
			filter.config = " . json_encode($arguments) . ";
			filter.name = 'categories';
			filter.value = $(this).val();
			add_customsearch_$id('$fieldid', filter); 
		});";
		
		foreach ($cats as $c) {
			$option = $document->createElement('option', $_categpath ? $c['categpath'] : $c['name']); 
			$option->setAttribute('value', $c['categId']);
			$element->appendChild($option);
			if ($default && in_array($c, (array) $default)) {
				$element->setAttribute('value', $default);
				$script .= "$('#$fieldid').trigger('change');";
			}
		}
	

	} 

	return $document->saveHTML();
}

function cs_design_select($id, $fieldname, $fieldid, $arguments, $default, &$script, &$groups) {
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
	// leave a blank one in the front
	if (!isset($arguments['multiple']) && !isset($arguments['size'])) {
		$option = $document->createElement('option');
		$element->appendChild($option);
	}

	$script .= "$('#$fieldid').change(function() {
			var filter = new Object();
			filter.config = " . json_encode($arguments) . ";
			filter.name = 'select';
			filter.value = $(this).val();
			add_customsearch_$id('$fieldid', filter);
		});"; 

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
			$script .= 	"$('#$fieldid').trigger('change');";
		}
		$element->appendChild($option); 
	}
	return $document->saveHTML(); 
}

