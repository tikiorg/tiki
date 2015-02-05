<?php
// (c) Copyright 2002-2014 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"], basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}


/*
 * Render fields of a trackeritem when called from the tracker
 * @param array $params - params passed from tempate as key/value pairs
 * Added keys to set the view/edit item template in tiki14. Format as defined in the tracker, i.e. 'wiki:myPageName, 'tpl:myTpl.tpl' 
 * 'viewItemPretty': define a template to view the item. 
 * 'editItemPretty': define a template to edit the item. 
 * These keys treat the template setting in the tracker as a default value and will therefore override if present.
 * They only apply if the default setting would apply - i.e sectionformat must be set to configured.
 */
function smarty_function_trackerfields($params, $smarty)
{
	if (! isset($params['fields']) || ! is_array($params['fields'])) {
		return tr('Invalid fields provided.');
	}

	if (! isset($params['trackerId']) || ! $definition = Tracker_Definition::get($params['trackerId'])) {
		return tr('Missing or invalid tracker reference.');
	}

	if (! isset($params['mode'])) {
		$params['mode'] = 'edit';
	}

	$smarty->loadPlugin('smarty_function_trackeroutput');
	$smarty->loadPlugin('smarty_function_trackerinput');

	$sectionFormat = $definition->getConfiguration('sectionFormat', 'flat');

	if (! empty($params['format'])) {
		$sectionFormat = $params['format'];
	}

	$trackerInfo = $definition->getInformation();
	$smarty->assign('tracker_info', $trackerInfo);
	$smarty->assign('status_types', TikiLib::lib('trk')->status_types());

	$title = tr('General');
	$sections = [];
	$auto = ['input' => [], 'output' => [], 'inline' => []];

	foreach ($params['fields'] as $field) {
		if ($field['type'] == 'h') {
			$title = tr($field['name']);
		} else {
			$sections[$title][] = $field;
		}
		$permName = $field['permName'];
		$auto['input'][$permName] = new Tiki_Render_Lazy(function () use ($field, $smarty) {
			return smarty_function_trackerinput([
				'field' => $field,
				'showlinks' => 'n',
				'list_mode' => 'n',
			], $smarty);
		});
		$auto['output'][$permName] = new Tiki_Render_Lazy(function () use ($field, $smarty) {
			return smarty_function_trackeroutput([
				'field' => $field,
				'showlinks' => 'n',
				'list_mode' => 'n',
			], $smarty);
		});
		if (isset($params['itemId'])) {
			$auto['inline'][$permName] = new Tiki_Render_Lazy(function () use ($field, $smarty, $params) {
				return smarty_function_trackeroutput([
					'field' => $field,
					'showlinks' => 'n',
					'list_mode' => 'n',
					'editable' => 'inline',
					'itemId' => $params['itemId'],
				], $smarty);
			});
		}
	}

	$out = array();
	foreach ($sections as $title => $fields) {
		$out[md5($title)] = array(
			'heading' => $title,
			'fields' => $fields,
		);
	}

	if ($params['mode'] == 'view') {
		$auto['default'] = $auto['output'];
	} else {
		$auto['default'] = $auto['input'];
	}
	
	// Compatibility attempt with the legacy $f_X format.
	foreach ($fields as $field) {
		$id = $field['fieldId'];
		$permName = $field['permName'];
		$smarty->assign('f_' . $id, $auto['default'][$permName]);
		// https://doc.tiki.org/Pretty+Tracker states that next to {f_id} also {f_fieldname} can be used. 
		// Somehow there is the support missing here - so add it		
		$smarty->assign('f_' . $permName, $auto['default'][$permName]);
	}
	
	// https://doc.tiki.org/Pretty+Tracker states that also that internal trackerfield names can be used
	/*
	{$f_created}: created date
	{$f_status_input}: status input field
	{$f_status}: status (output)
	{$f_itemId}: the item id
	{$f_lastmodif}: last modified date (this will display unix date, for human readable date look below)
	(In Tiki 8 onwards) {$itemoff}: the iteration number of each item
	{$tr_offset}: the offset of the item, i.e. this is the nth item of the total number of x items
	*/
	// @TODO need to add support
	
	

	$smarty->assign('sections', array_values($out));
	$smarty->assign('fields', $params['fields']);
	$smarty->assign('auto', $auto);

	$editItemPretty = isset($params['editItemPretty']) ? $params['editItemPretty'] : '';
	$viewItemPretty = isset($params['viewItemPretty']) ? $params['viewItemPretty'] : '';

	$trklib = TikiLib::lib('trk');
	$trklib->registerSectionFormat('config', 'edit', $editItemPretty, tr('Configured'));
	$trklib->registerSectionFormat('config', 'view', $viewItemPretty, tr('Configured'));
	$template = $trklib->getSectionFormatTemplate($sectionFormat, $params['mode']);

	$trklib->unregisterSectionFormat('config');

	return $smarty->fetch($template);
}

