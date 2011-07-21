<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function wikiplugin_trackerlist_info() {
	return array(
		'name' => tra('Tracker List'),
		'documentation' => tra('PluginTrackerList'),
		'description' => tra('Displays the output of a tracker content, fields are indicated with numeric ids.'),
		'prefs' => array( 'feature_trackers', 'wikiplugin_trackerlist' ),
		'body' => tra('Notice'),
		'icon' => 'pics/icons/database_table.png',
		'filter' => 'text',
		'params' => array(
			'trackerId' => array(
				'required' => true,
				'name' => tra('Tracker ID'),
				'description' => tra('Numeric value representing the tracker ID'),
				'filter' => 'digits',
				'default' => '',
			),
			'fields' => array(
				'required' => false,
				'name' => tra('Fields'),
				'description' => tra('Colon-separated list of field IDs for the fields to be displayed. Example: 2:4:5'),
				'filter' => 'digits',
				'separator' => ':',
				'default' => '',
			),
			'sort' => array(
				'required' => false,
				'name' => tra('Sort'),
				'description' => tra('Display columns in the order listed in the fields parameter instead of by field ID (field ID order is used by default'),
				'filter' => 'alpha',
				'default' => 'n',
				'options' => array(
					array('text' => '', 'value' => ''), 
					array('text' => tra('Yes'), 'value' => 'y'), 
					array('text' => tra('No'), 'value' => 'n')
				)
			),
			'popup' => array(
				'required' => false,
				'name' => tra('Popup'),
				'description' => tra('Colon-separated list of fields which will display in a tooltip on mouse over. Example: 6:7'),
				'filter' => 'digits',
				'separator' => ':',
				'default' => '',
			),
			'stickypopup' => array(
				'required' => false,
				'name' => tra('Sticky Popup'),
				'description' => tra('Choose whether the tooltip will stay displayed on mouse out (does not stay open by default)'),
				'filter' => 'alpha',
				'default' => '',
				'options' => array(
					array('text' => '', 'value' => ''), 
					array('text' => tra('Yes'), 'value' => 'y'), 
					array('text' => tra('No'), 'value' => 'n')
				)
			),
			'showtitle' => array(
				'required' => false,
				'name' => tra('Show Title'),
				'description' => tra('Display the title of the tracker (not shown by default)'),
				'filter' => 'alpha',
				'default' => 'n',
				'options' => array(
					array('text' => '', 'value' => ''), 
					array('text' => tra('Yes'), 'value' => 'y'), 
					array('text' => tra('No'), 'value' => 'n')
				)
			),
			'showlinks' => array(
				'required' => false,
				'name' => tra('Show Links'),
				'description' => tra('Show links to each tracker item (not shown by default)'),
				'filter' => 'alpha',
				'default' => 'n',
				'options' => array(
					array('text' => '', 'value' => ''), 
					array('text' => tra('Yes'), 'value' => 'y'), 
					array('text' => tra('No'), 'value' => 'n')
				)
			),
			'showdesc' => array(
				'required' => false,
				'name' => tra('Show Description'),
				'description' => tra('Show the tracker\'s description (not shown by default)'),
				'filter' => 'alpha',
				'default' => 'n',
				'options' => array(
					array('text' => '', 'value' => ''), 
					array('text' => tra('Yes'), 'value' => 'y'), 
					array('text' => tra('No'), 'value' => 'n')
				)
			),
			'shownbitems' => array(
				'required' => false,
				'name' => tra('Show Item Count'),
				'description' => tra('Show the number of items found (not shown by default)'),
				'filter' => 'alpha',
				'default' => 'n',
				'options' => array(
					array('text' => '', 'value' => ''), 
					array('text' => tra('Yes'), 'value' => 'y'), 
					array('text' => tra('No'), 'value' => 'n')
				)
			),
			'showinitials' => array(
				'required' => false,
				'name' => tra('Show Initials'),
				'description' => tra('Show an alphabetical index by first letter to assist in navigation (not shown by default)'),
				'filter' => 'alpha',
				'default' => 'n',
				'options' => array(
					array('text' => '', 'value' => ''), 
					array('text' => tra('Yes'), 'value' => 'y'), 
					array('text' => tra('No'), 'value' => 'n')
				)
			),
			'showstatus' => array(
				'required' => false,
				'name' => tra('Show Status'),
				'description' => tra('Show the status of the items (not shown by default)'),
				'filter' => 'alpha',
				'default' => 'n',
				'options' => array(
					array('text' => '', 'value' => ''), 
					array('text' => tra('Yes'), 'value' => 'y'), 
					array('text' => tra('No'), 'value' => 'n')
				)
			),
			'showcreated' => array(
				'required' => false,
				'name' => tra('Show Creation Date'),
				'description' => tra('Creation date display is based on tracker settings unless overriden here'),
				'filter' => 'alpha',
				'default' => '',
				'options' => array(
					array('text' => '', 'value' => ''), 
					array('text' => tra('Yes'), 'value' => 'y'), 
					array('text' => tra('No'), 'value' => 'n')
				)
			),
			'showlastmodif' => array(
				'required' => false,
				'name' => tra('Last Modification Date'),
				'description' => tra('Last modification date display is based on tracker settings unless overriden here'),
				'filter' => 'alpha',
				'default' => '',
				'options' => array(
					array('text' => '', 'value' => ''), 
					array('text' => tra('Yes'), 'value' => 'y'), 
					array('text' => tra('No'), 'value' => 'n')
				)
			),
			'showfieldname' => array(
				'required' => false,
				'name' => tra('Show Field Name'),
				'description' => tra('Use the field names as column titles (used by default)'),
				'filter' => 'alpha',
				'default' => 'y',
				'options' => array(
					array('text' => '', 'value' => ''), 
					array('text' => tra('Yes'), 'value' => 'y'), 
					array('text' => tra('No'), 'value' => 'n')
				)
			),
			'showitemrank' => array(
				'required' => false,
				'name' => tra('Show Item Rank'),
				'description' => tra('Show item ranks (not shown by default)'),
				'filter' => 'alpha',
				'default' => 'n',
				'options' => array(
					array('text' => '', 'value' => ''), 
					array('text' => tra('Yes'), 'value' => 'y'), 
					array('text' => tra('No'), 'value' => 'n')
				)
			),
			'status' => array(
				'required' => false,
				'name' => tra('Status Filter'),
				'description' => tra('Only show items matching certain status filters (only items with open status shown by default)'),
				'filter' => 'alpha',
				'default' => 'o',
				'options' => array(
					array('text' => '', 'value' => ''), 
					array('text' => tra('Open'), 'value' => 'o'), 
					array('text' => tra('Pending'), 'value' => 'p'), 
					array('text' => tra('Closed'), 'value' => 'c'), 
					array('text' => tra('Open & Pending'), 'value' => 'op'), 
					array('text' => tra('Open & Closed'), 'value' => 'oc'), 
					array('text' => tra('Pending & Closed'), 'value' => 'pc'), 
					array('text' => tra('Open, Pending & Closed'), 'value' => 'opc')
				)
			),
			'sort_mode' => array(
				'required' => false,
				'name' => tra('Sort Mode'),
				'description' => tra('Sort rows in ascending or descending order based on field ID, date created or date last modified using these values: ') . 
									'created_asc, created_desc, lastModif_asc, lastModif_desc, f_fieldId_asc, f_filedId_desc ' . tra('(replacing fieldId with the field ID number).'),
				'filter' => 'word',
				'default' => '',
			),
			'sortchoice' => array(
				'required' => false,
				'name' => tra('Sort Choice'),
				'description' => tra('Add a dropdown of sorting choices. Example with two sorting choices: created_desc|Newest first: lastModif_desc|Last modified first'),
				'filter' => 'text',
				'separator' => ':',
				'default' => '',
			),
			'max' => array(
				'required' => false,
				'name' => tra('Maximum Items'),
				'description' => tra('Maximum number of items to display. Defaults to max records preference, if set.'),
				'filter' => 'int',
				'default' => '',
			),
			'offset' => array(
				'required' => false,
				'name' => tra('Offset'),
				'description' => tra('Offset of first item. Default is no offset.'),
				'filter' => 'int',
				'default' => 0,
			),
			'showpagination' => array(
				'required' => false,
				'name' => tra('Show Pagination'),
				'description' => tra('Determines whether pagination will be shown (shown by default)'),
				'filter' => 'alpha',
				'default' => 'y',
				'options' => array(
					array('text' => '', 'value' => ''), 
					array('text' => tra('Yes'), 'value' => 'y'), 
					array('text' => tra('No'), 'value' => 'n')
				)
			),
			'filterfield' => array(
				'required' => false,
				'name' => tra('Filter Field'),
				'description' => tra('Colon separated list of fields to allow filtering on.'),
				'filter' => 'digits',
				'separator' => ':',
				'default' => '',
			),
			'filtervalue' => array(
				'required' => false,
				'name' => tra('Filter Value'),
				'description' => tra('Filter value of the filterfield. For better performance, use exactvalue instead'),
				'filter' => 'text',
				'separator' => ':',
				'default' => '',
			),
			'exactvalue' => array(
				'required' => false,
				'name' => tra('Exact Value'),
				'description' => tra('Exact value of the filter'),
				'filter' => 'text',
				'separator' => ':',
				'default' => '',
			),
			'checkbox' => array(
				'required' => false,
				'name' => tra('Checkbox'),
				'description' => tra('Adds a checkbox on each line to be able to do an action.') . '<br />' .
								tra('e.g. fieldId/postName/Title/Submit/ActionUrl/tpl/radio|dropdown') . '<br />' ,
				'advanced' => true,
				'default' => '',
			),
			'goIfOne' => array(
				'required' => false,
				'name' => tra('goIfOne'),
				'description' => tra('Go directly to tiki-view_tracker_item.php if only one item is found'),
				'filter' => 'alpha',
				'advanced' => true,
				'default' => '',
				'options' => array(
					array('text' => '', 'value' => ''), 
					array('text' => tra('Yes'), 'value' => 'y'), 
					array('text' => tra('No'), 'value' => 'n')
				)
			),
			'more' => array(
				'required' => false,
				'name' => tra('More'),
				'description' => tra('Show a \'more\' button that links to tiki-view_tracker.php (not shown by default)'),
				'filter' => 'alpha',
				'default' => 'n',
				'options' => array(
					array('text' => '', 'value' => ''), 
					array('text' => tra('Yes'), 'value' => 'y'), 
					array('text' => tra('No'), 'value' => 'n')
				)
			),
			'moreurl' => array(
				'required' => false,
				'name' => tra('More URL'),
				'description' => tra('More link pointing to specified URL instead of default tracker item link'),
				'filter' => 'url',
				'default' => 'tiki-view_tracker.php',
			),
			'view' => array(
				'required' => false,
				'name' => tra('View'),
				'description' => tra('Display only the items of the current user or the current page name'),
				'filter' => 'alpha',
				'advanced' => true,
				'default' => '',
				'options' => array(
					array('text' => '', 'value' => ''), 
					array('text' => tra('Page'), 'value' => 'page'), 
					array('text' => tra('User'), 'value' => 'user')
				)
			),
			'tpl' => array(
				'required' => false,
				'name' => tra('Template File'),
				'description' => tra('Use content of the tpl file as template to display the item'),
				'advanced' => true,
				'default' => '',
			),
			'wiki' => array(
				'required' => false,
				'name' => tra('Wiki Page'),
				'description' => tra('Use content of the wiki page as template to display the item. The page should have the permission tiki_p_use_as_template set, and that page should be only open for edition to fully trusted users such as other site admins'),
				'filter' => 'pagename',
				'advanced' => true,
				'default' => '',
			),
			'tplwiki' => array(
				'required' => false,
				'name' => tra('Template file in a Wiki page'),
				'description' => tra('Use content of the wiki page as template to display the item but with as little parsing on the content as with a tpl on disk. The page should have the permission tiki_p_use_as_template set, and that page should be only open for edition to fully trusted users such as other site admins'),
				'filter' => 'pagename',
				'advanced' => true,
				'default' => '',
			),
			'view_user' => array(
				'required' => false,
				'name' => tra('View User'),
				'description' => tra('Will display the items of the specified user'),
				'default' => '',
			),
			'itemId' => array(
				'required' => false,
				'name' => tra('Item ID separated with :'),
				'description' => tra('To restrict the list to these item IDs'),
				'filter' => 'digits',
				'separator' => ':',
				'default' => '',
			),
			'ignoreRequestItemId' => array(
				'required' => false,
				'name' => tra('Ignore ItemId'),
				'description' => tra('Ignore the itemId url parameter when filtering list (not ignored by default)'),
				'filter' => 'alpha',
				'default' => 'n',
				'advanced' => true,
				'options' => array(
					array('text' => '', 'value' => ''), 
					array('text' => tra('Yes'), 'value' => 'y'), 
					array('text' => tra('No'), 'value' => 'n')
				)
			),
			'url' => array(
				'required' => false,
				'name' => tra('URL'),
				'description' => tra('The link that will be on each main field'),
				'filter' => 'url',
				'default' => '',
			),
			'ldelim' => array(
				'required' => false,
				'name' => tra('Left Deliminator'),
				'description' => tra('Smarty left delimiter for Latex generation'),
				'advanced' => true,
				'default' => '',
			),
			'rdelim' => array(
				'required' => false,
				'name' => tra('Right Deliminator'),
				'description' => tra('Smarty right delimiter for Latex generation'),
				'advanced' => true,
				'default' => '',
			),
			'list_mode' => array(
				'required' => false,
				'name' => tra('List Mode'),
				'description' => tra('Set output format. Yes (y) displays tracker list view with truncated values (default); 
										No (n) displays in tracker item view; Comma Separated Values (csv) outpits without any HTML formatting.'),
				'filter' => 'alpha',
				'default' => 'y',
				'options' => array(
					array('text' => '', 'value' => ''), 
					array('text' => tra('Yes'), 'value' => 'y'), 
					array('text' => tra('No'), 'value' => 'n'),
					array('text' => tra('Comma Separated Values'), 'value' => 'csv')
					)
			),
			'export' => array(
				'required' => false,
				'name' => tra('Export Button'),
				'description' => tra('Show an export button (not shown by default)'),
				'filter' => 'alpha',
				'advanced' => true,
				'default' => 'n',
				'options' => array(
					array('text' => '', 'value' => ''), 
					array('text' => tra('Yes'), 'value' => 'y'), 
					array('text' => tra('No'), 'value' => 'n')
				)
			),
			'compute' => array(
				'required' => false,
				'name' => tra('Compute'),
				'description' => tra('Sum or average all the values of a field and displays it at the bottom of the table.').' '.tra('fieldId').'/sum:'.tra('fieldId').'/avg',
				'filter' => 'text',
				'advanced' => true,
				'default' => '',
			),
			'silent' => array(
				'required' => false,
				'name' => tra('Silent'),
				'description' => tra('Show nothing if no items found (the table header and a \'No records found\' message is shown by default).'),
				'filter' => 'alpha',
				'default' => '',
				'options' => array(
					array('text' => '', 'value' => ''), 
					array('text' => tra('Yes'), 'value' => 'y'), 
					array('text' => tra('No'), 'value' => 'n')
				)
			),
			'showdelete' => array(
				'required' => false,
				'name' => tra('Show Delete'),
				'description' => tra('Show a delete option (not shown by default)'),
				'filter' => 'alpha',
				'default' => 'n',
				'options' => array(
					array('text' => '', 'value' => ''), 
					array('text' => tra('Yes'), 'value' => 'y'), 
					array('text' => tra('No'), 'value' => 'n')
				)
			),
			'showwatch' => array(
				'required' => false,
				'name' => tra('Show Watch Button'),
				'description' => tra('Show a watch button (not shown by default)'),
				'filter' => 'alpha',
				'default' => '',
				'options' => array(
					array('text' => '', 'value' => ''), 
					array('text' => tra('Yes'), 'value' => 'y'), 
					array('text' => tra('No'), 'value' => 'n')
				)
			),
			'showrss' => array(
				'required' => false,
				'name' => tra('Show Feed Button'),
				'description' => tra('Show an RSS feed button (not shown by default)'),
				'filter' => 'alpha',
				'default' => 'n',
				'options' => array(
					array('text' => '', 'value' => ''), 
					array('text' => tra('Yes'), 'value' => 'y'), 
					array('text' => tra('No'), 'value' => 'n')
				)
			),
			'googlemap' => array(
				'required' => false,
				'name' => tra('Show Google Map'),
				'description' => tra('Show Google Map of results (not shown by default)'),
				'filter' => 'alpha',
				'default' => '',
				'options' => array(
					array('text' => '', 'value' => ''), 
					array('text' => tra('Yes'), 'value' => 'y'), 
					array('text' => tra('No'), 'value' => 'n')
				)
			),
			'googlemapicon' => array(
				'required' => false,
				'name' => tra('Google Map Icon'),
				'description' => tra('Url of default icon to use for markers on the map'),
				'filter' => 'url',
				'default' => '',
				'parent' => array('name' => 'googlemap', 'value' => 'y')
			),
			'calendarfielddate' => array(
				'required' => false,
				'name' => tra('Calendar Field IDs'),
				'description' => tra('Used to display items in a calendar view. One fieldId if one date, or 2 fieldIds separated with : for start:end'),
				'separator' => ':',
				'filter' => 'digits',
				'default' => '',
			),
			'calendarviewmode' => array(
				'required' => false,
				'name' => tra('Calendar View Mode'),
				'description' => tra('Calendar view type time span (default is month)'),
				'filter' => 'word',
				'default' => 'month',
				'options' => array(
					array('text' => '', 'value' => ''), 
					array('text' => tra('Month'), 'value' => 'month'), 
					array('text' => tra('Bimester'), 'value' => 'bimester'), 
					array('text' => tra('Trimester'), 'value' => 'trimester'), 
					array('text' => tra('Quarter'), 'value' => 'quarter'), 
					array('text' => tra('Semester'), 'value' => 'semester'), 
					array('text' => tra('Year'), 'value' => 'year')
				)
			),
			'calendarstickypopup' => array(
				'required' => false,
				'name' => tra('Sticky Popup'),
				'description' => tra('Calendar item pop-ups will stay open if set to y (Yes). Set to n (No) by default.'),
				'filter' => 'alpha',
				'default' => 'n',
				'options' => array(
					array('text' => '', 'value' => ''), 
					array('text' => tra('Yes'), 'value' => 'y'), 
					array('text' => tra('No'), 'value' => 'n')
				)
			),
			'calendarbeginmonth' => array(
				'required' => false,
				'name' => tra('Beginning of Month'),
				'description' => tra('Set whether calendar will begin at the beginning of the month (does by default).'),
				'filter' => 'alpha',
				'default' => 'y',
				'options' => array(
					array('text' => '', 'value' => ''), 
					array('text' => tra('Yes'), 'value' => 'y'), 
					array('text' => tra('No'), 'value' => 'n')
				)
			),
			'calendarviewnavbar' => array(
				'required' => false,
				'name' => tra('Navigation Bar'),
				'description' => tra('Show calendar navigation bar (shown by default).'),
				'filter' => 'alpha',
				'default' => 'y',
				'options' => array(
					array('text' => '', 'value' => ''), 
					array('text' => tra('Yes'), 'value' => 'y'), 
					array('text' => tra('No'), 'value' => 'n'), 
					array('text' => tra('Partial'), 'value' => 'partial')
				)
			),
			'calendartitle' => array(
				'required' => false,
				'name' => tra('Calendar Title'),
				'description' => tra('Enter a title to display a calendar title (not set by default)'),
				'filter' => 'text',
				'default' => '',
			),
			'calendardelta' => array(
				'required' => false,
				'name' => tra('Calendar Delta'),
				'description' => tra('Set the calendar delta that will be shown (not set by default)'),
				'filter' => 'text',
				'default' => '',
				'options' => array(
					array('text' => '', 'value' => ''), 
					array('text' => tra('Plus Month'), 'value' => '+month'), 
					array('text' => tra('Minus Month'), 'value' => '-month'), 
					array('text' => tra('Plus Bimester'), 'value' => '+bimester'),
					array('text' => tra('Minus Bimester'), 'value' => '-bimester')
				)
			),
			'displaysheet' => array(
				'required' => false,
				'name' => tra('Display Spreadsheet.'),
				'description' => tra('Display tracker as a spreadsheet (not used by default)'),
				'filter' => 'alpha',
				'default' => 'n',
				'options' => array(
					array('text' => '', 'value' => ''), 
					array('text' => tra('Yes'), 'value' => 'y'), 
					array('text' => tra('No'), 'value' => 'n')
				)
			),
			'force_compile' => array(
				'required' => false,
				'name' => tra('Force Compile.'),
				'description' => tra('Force Smarty to recompile the templates for each tracker item when using a wiki page as a template. Default=n (best performance)'),
				'filter' => 'alpha',
				'default' => 'n',
				'advanced' => true,
				'options' => array(
					array('text' => '', 'value' => ''), 
					array('text' => tra('Yes'), 'value' => 'y'), 
					array('text' => tra('No'), 'value' => 'n')
				)
			)
		)
	);
}

function wikiplugin_trackerlist($data, $params) {
	global $smarty, $tikilib, $dbTiki, $userlib, $tiki_p_admin_trackers, $prefs, $_REQUEST, $tiki_p_view_trackers, $user, $page, $tiki_p_tracker_vote_ratings, $tiki_p_tracker_view_ratings, $trklib, $tiki_p_traker_vote_rating, $tiki_p_export_tracker, $tiki_p_watch_trackers;
	require_once("lib/trackers/trackerlib.php");
	global $notificationlib;  include_once('lib/notifications/notificationlib.php');//needed if plugin tracker after plugin trackerlist
	static $iTRACKERLIST = 0;
	++$iTRACKERLIST;
	$smarty->assign('iTRACKERLIST', $iTRACKERLIST);
	$default = array('calendarfielddate' => '', 'wiki' => '', 'calendarviewmode' => 'month', 'calendarstickypopup' => 'n',
				'calendarbeginmonth' => 'y', 'calendarviewnavbar' => 'y', 'calendartitle'=>'', 'calendardelta' => '', 'force_compile' => 'n');
	$params = array_merge($default, $params);
	
	extract ($params,EXTR_SKIP);

	if ($prefs['feature_trackers'] != 'y' || !isset($trackerId) || !($tracker_info = $trklib->get_tracker($trackerId))) {
		return $smarty->fetch("wiki-plugins/error_tracker.tpl");
	} else {

		global $auto_query_args;
		$auto_query_args_local = array('trackerId', 'tr_initial',"tr_sort_mode$iTRACKERLIST",'tr_user', 'filterfield', 'filtervalue', 'exactvalue');
		$auto_query_args = empty($auto_query_args)? $auto_query_args_local: array_merge($auto_query_args, $auto_query_args_local);
		$smarty->assign('trackerId', $trackerId);
		$tracker_info = $trklib->get_tracker($trackerId);
		if ($t = $trklib->get_tracker_options($trackerId)) {
			$tracker_info = array_merge($tracker_info, $t);
		}

		if (!isset($sort)) {
			$sort = 'n';
		}

		if ($tiki_p_admin_trackers != 'y') {
			$perms = $tikilib->get_perm_object($trackerId, 'tracker', $tracker_info, false);
			if ($perms['tiki_p_view_trackers'] != 'y' && !$user) {
				return;
			}
			$userCreatorFieldId = $trklib->get_field_id_from_type($trackerId, 'u', '1%');
			$groupCreatorFieldId = $trklib->get_field_id_from_type($trackerId, 'g', '1%');
			if ($perms['tiki_p_view_trackers'] != 'y' && $tracker_info['writerCanModify'] != 'y' && empty($userCreatorFieldId) && empty($groupCreatorFieldId)) {
				return;
			}
			$smarty->assign_by_ref('perms', $perms);
		}

		global $trklib; require_once("lib/trackers/trackerlib.php");
		if (!empty($fields)) {
			$limit = $fields;
		} else {
			$limit = '';
		}
		if (!empty($filterfield) && !empty($limit)) {
			$limit = array_unique(array_merge($limit, $filterfield));
		}
		if (!empty($popup)) {
			$limit = array_unique(array_merge($limit, $popup));
		}
		if (!empty($calendarfielddate)) {
			$limit = array_unique(array_merge($limit, $calendarfielddate));
		}
		if (!empty($limit) && $trklib->test_field_type($limit, array('C'))) {
			$limit = '';
		}
		$allfields = $trklib->list_tracker_fields($trackerId, 0, -1, 'position_asc', '', true, '', $trklib->flaten($limit));
		if (!empty($fields)) {
			$listfields = $fields;
			if ($sort == 'y') {
				$allfields = $trklib->sort_fields($allfields, $listfields);
			}
		} elseif (!empty($wiki) || !empty($tpl) || !empty($tplwiki)) {
				if (!empty($wiki)) {
					$listfields = $trklib->get_pretty_fieldIds($wiki, 'wiki');
				} elseif (!empty($tplwiki)) {
					$listfields = $trklib->get_pretty_fieldIds($tplwiki, 'wiki');
				} else {
					$listfields = $trklib->get_pretty_fieldIds($tpl, 'tpl');
				}
		} else {
			$listfields = '';
		}
		if (!empty($compute) && !empty($listfields)) {
			if (preg_match_all('/[0-9.]+/', $compute, $matches)) {
				foreach ($matches[0] as $f) {
					if (!in_array($f, $listfields))
						$listfields[] = $f;
				}
			}
		}
		if (!empty($filterfield)) {
			if (is_array($filterfield)) {
				foreach ($filterfield as $ff) {
					unset($filterfieldok);
					if (is_array($ff)) {// already checked in trackerfilter
						$filterfieldok=true;
						break;
					} else {
						foreach ($allfields['data'] as $f) {
							if ($f['fieldId'] == $ff) {
								$filterfieldok=true;
								break;
							}
						}
					}
					if (!isset($filterfieldok))
						break;
				}
			} else {
				foreach ($allfields['data'] as $f) {
					if ($f['fieldId'] == $filterfield) {
						$filterfieldok=true;
						break;
					}
				}
			}
			if (!isset($filterfieldok)) {
				return tra('incorrect filterfield');
			}
		}
		if (isset($_REQUEST['reloff']) && empty($_REQUEST['itemId']) && !empty($_REQUEST['trackerId'])) { //coming from a pagination
			$items = $trklib->list_items($_REQUEST['trackerId'], $_REQUEST['reloff'], 1, '', '', isset($_REQUEST['filterfield'])?preg_split('/\s*:\s*/',$_REQUEST['filterfield']):'', isset($_REQUEST['filtervalue'])? preg_split('/\s*:\s*/', $_REQUEST['filtervalue']):'', isset($_REQUEST['status'])? preg_split('/\s*:\s*/', $_REQUEST['status']):'', isset($_REQUEST['initial'])?$_REQUEST['initial']:'', isset($_REQUEST['exactvalue'])?preg_split('/\s*:\s*/', $_REQUEST['exactvalue']):'');
			if (isset($items['data'][0]['itemId'])) {
				$_REQUEST['cant'] = $items['cant'];
				$_REQUEST['itemId'] = $items['data'][0]['itemId'];
			}
		}

		if (!empty($_REQUEST['itemId']) && $tiki_p_tracker_vote_ratings == 'y' && $user) {
			$hasVoted = false;
			foreach ($allfields['data'] as $f) {
				if ($f['type'] == 's' && isset($tracker_info['useRatings']) and $tracker_info['useRatings'] == 'y' && ($f['name'] == 'Rating' || $f['name'] = tra('Rating'))) {
					$i = $f['fieldId'];
					if (isset($_REQUEST["ins_$i"]) && ($_REQUEST["ins_$i"] == 'NULL' || in_array($_REQUEST["ins_$i"], explode(',',$tracker_info['ratingOptions'])))) {
						$trklib->replace_rating($trackerId, $_REQUEST['itemId'], $i, $user, $_REQUEST["ins_$i"]);
						$hasVoted = true; 
					}
				} elseif ($f['type'] == '*') {
					$i = $f['fieldId'];
					if (isset($_REQUEST["ins_$i"])) {
						$trklib->replace_star($_REQUEST["ins_$i"], $trackerId, $_REQUEST['itemId'], $f, $user);
						$hasVoted = true;
					}
				}
			}
			if ($hasVoted) {
				// Must strip NULL for remove my vote case
				$url = preg_replace('/[(\?)|&]vote=y/', '$1', preg_replace('/[(\?)|&]itemId=[0-9]+/', '$1', preg_replace('/[(\?)|&]ins_[0-9]+=-?[0-9|N|U|L]*/', '$1', $_SERVER['REQUEST_URI'])));
				header("Location: $url");
				die;
			}
		}

		if (!empty($showwatch) && $showwatch == 'y' && $prefs['feature_user_watches'] == 'y' && $tiki_p_watch_trackers == 'y' && !empty($user)) {
			if (isset($_REQUEST['watch']) && isset($_REQUEST['trackerId']) && $_REQUEST['trackerId'] == $trackerId) {
				if ($_REQUEST['watch'] == 'add') { 
					$tikilib->add_user_watch($user, 'tracker_modified', $trackerId, 'tracker', $tracker_info['name'], "tiki-view_tracker.php?trackerId=" . $trackerId);
				} elseif ($_REQUEST['watch'] == 'stop') {
					$tikilib->remove_user_watch($user, 'tracker_modified', $trackerId, 'tracker');
				}
			}
			if ($tikilib->user_watches($user, 'tracker_modified', $trackerId, 'tracker')) {
				$smarty->assign('user_watching_tracker', 'y');
			} else {
				$smarty->assign('user_watching_tracker', 'n');
			}
		} else {
			$smarty->clear_assign('user_watching_tracker');
		}
		if (empty($showrss) || $showrss == 'n') {
			$smarty->assign('showrss', 'n');
		} else {
			$smarty->assign('showrss', 'y');
		}

		if (empty($listfields)) {
			foreach($allfields['data'] as $f) {
				$listfields[] = $f['fieldId'];
			}
		}
		if (!empty($popup)) {
			$popupfields = $popup;
		} else {
			$popupfields = array();
		}
		if ($t = $trklib->get_tracker_options($trackerId))
			$tracker_info = array_merge($tracker_info, $t);
		$smarty->assign_by_ref('tracker_info', $tracker_info);
		
		//$query_array = array();
		//$quarray = array();
		//TikiLib::parse_str($_SERVER['QUERY_STRING'],$query_array);

		if (isset($stickypopup) && $stickypopup == 'y') {
			$stickypopup = true;
		} else {
			$stickypopup = false;
		}
		$smarty->assign_by_ref('stickypopup', $stickypopup);

		if (!isset($showtitle)) {
			$showtitle = 'n';
		}
		$smarty->assign_by_ref('showtitle', $showtitle);
		
		if (!isset($showlinks)) {
			$showlinks = 'n';
		}
		$smarty->assign_by_ref('showlinks', $showlinks);
		
		if (!isset($showdesc)) {
			$showdesc = 'n';
		}
		$smarty->assign_by_ref('showdesc', $showdesc);
		
		if (!isset($showinitials)) {
			$showinitials = 'n';
		}
		$smarty->assign_by_ref('showinitials', $showinitials);

		if (!isset($shownbitems)) {
			$shownbitems = 'n';
		}
		$smarty->assign_by_ref('shownbitems', $shownbitems);
		
		if (!isset($showstatus)) {
			$showstatus = 'n';
		}
		$smarty->assign_by_ref('showstatus', $showstatus);

		if (!isset($showfieldname)) {
			$showfieldname = 'y';
		}
		$smarty->assign_by_ref('showfieldname', $showfieldname);

		if (!isset($showitemrank)) {
			$showitemrank = 'n';
		}
		$smarty->assign_by_ref('showitemrank', $showitemrank);

		if (!isset($showdelete)) {
			$showdelete = 'n';
		}
		$smarty->assign_by_ref('showdelete', $showdelete);
		if (!isset($showpagination)) {
			$showpagination = 'y';
		}
		$smarty->assign_by_ref('showpagination', $showpagination);
		if (!isset($sortchoice)) {
			$sortchoice = '';
		} else {
			foreach ($sortchoice as $i=>$sc) {
				$sc = explode('|', $sc);
				$sortchoice[$i] = array('value'=>$sc[0], 'label'=>empty($sc[1])?$sc[0]: $sc[1]);
			}
		}
		$smarty->assign_by_ref('sortchoice', $sortchoice);

		if (!isset($status)) {
			$status = 'o';
		}
		$tr_status = $status;
		$smarty->assign_by_ref('tr_status', $tr_status);
		if (!isset($list_mode)) {
			$list_mode = 'y';
		}
		$smarty->assign_by_ref('list_mode', $list_mode);

		if (!isset($showcreated)) {
			$showcreated = $tracker_info['showCreated'];
		}
		$smarty->assign_by_ref('showcreated', $showcreated);
		if (!isset($showlastmodif)) {
			$showlastmodif = $tracker_info['showLastModif'];
		}
		$smarty->assign_by_ref('showlastmodif', $showlastmodif);
		if (!isset($more))
			$more = 'n';
		$smarty->assign_by_ref('more', $more);
		if (!isset($moreurl))
			$moreurl = 'tiki-view_tracker.php';
		$smarty->assign_by_ref('moreurl', $moreurl);
		if (!isset($url))
			$url = '';
		$smarty->assign_by_ref('url', $url);
		if (!isset($export))
			$export = 'n';
		$smarty->assign_by_ref('export', $export);

		if (!empty($ldelim))
			$smarty->left_delimiter = $ldelim;
		if (!empty($rdelim))
			$smarty->right_delimiter = $rdelim;

		if (isset($checkbox)) {
			$check = array('ix' => -1, 'type' => 'checkbox');
			$cb = explode('/', $checkbox);
			
			if (isset($cb[0]))
				$check['fieldId'] = $cb[0];
			if (isset($cb[1]))
				$check['name'] = $cb[1];
			if (isset($cb[2]))
				$check['title'] = $cb[2];
			if (isset($cb[3]))
				$check['submit'] = $cb[3];
			if (isset($cb[4]))
				$check['action'] = $cb[4];
			if (isset($cb[5]))
				$check['tpl'] = $cb[5];
			if (isset($cb[6]) && $cb[6] == 'radio') {
				$check['radio'] = 'y';
				$check['type'] = 'radio';
			}
			if (isset($cb[6]) && $cb[6] == 'dropdown')
				$check['dropdown'] = 'y';				// is this actually used?
			
			$smarty->assign_by_ref('checkbox', $check);
		}	

		if (isset($_REQUEST["tr_sort_mode$iTRACKERLIST"])) {
			$sort_mode = $_REQUEST["tr_sort_mode$iTRACKERLIST"];
		} elseif (!isset($sort_mode)) {
			if (!empty($tracker_info['defaultOrderKey'])) {
				if ($tracker_info['defaultOrderKey'] == -1)
					$sort_mode = 'lastModif';
				elseif ($tracker_info['defaultOrderKey'] == -2)
					$sort_mode = 'created';
				elseif ($tracker_info['defaultOrderKey'] == -3)
					$sort_mode = 'itemId';
				else
					$sort_mode = 'f_'.$tracker_info['defaultOrderKey'];
				if (isset($tracker_info['defaultOrderDir'])) {
					$sort_mode.= "_".$tracker_info['defaultOrderDir'];
				} else {
					$sort_mode.= "_asc";
				}
			} else {
				$sort_mode = '';
			}
		} elseif ($sort_mode != 'created_asc' && $sort_mode != 'lastModif_asc' && $sort_mode != 'created_desc' && $sort_mode != 'lastModif_desc' && !preg_match('/f_[0-9]+_(asc|desc)/', $sort_mode)) {
			return tra('Incorrect param').' sort_mode';
		}

		$tr_sort_mode = $sort_mode;
		$smarty->assign_by_ref('tr_sort_mode', $tr_sort_mode);
		
		if (isset($compute)) {
			$max = -1; // to avoid confusion compute is on what you see or all the items
		} elseif (!isset($max)) {
			$max = $prefs['maxRecords'];
		}

		if (isset($_REQUEST['tr_offset'])) {
			$tr_offset = $_REQUEST['tr_offset'];
		} else if (isset($offset) && $offset >= 0) {
			$tr_offset = $offset;
		} else {
			$tr_offset = 0;
		}
		$smarty->assign_by_ref('tr_offset',$tr_offset);

			
		$tr_initial = '';
		if ($showinitials == 'y') {
			if (isset($_REQUEST['tr_initial'])) {
			  //$query_array['tr_initial'] = $_REQUEST['tr_initial'];
				$tr_initial = $_REQUEST['tr_initial'];
			}
			$smarty->assign('initials', explode(' ','a b c d e f g h i j k l m n o p q r s t u v w x y z'));
		}
		$smarty->assign_by_ref('tr_initial', $tr_initial);

		if ((isset($view) && $view == 'user') || isset($view_user) || isset($_REQUEST['tr_user'])) {
			if ($f = $trklib->get_field_id_from_type($trackerId, 'u', '1%')) {
				$filterfield[] = $f;
				$filtervalue[] = '';
				if (!isset($_REQUEST['tr_user'])) {
					$exactvalue[] = isset($view)? (empty($user)?'Anonymous':$user): $view_user;
				} else {
					$exactvalue[] = $_REQUEST['tr_user'];
					$smarty->assign_by_ref('tr_user', $exactvalue);
				}
			}
		}
		if (isset($view) && $view == 'page' && isset($_REQUEST['page'])) {
			if (($f = $trklib->get_field_id_from_type($trackerId, 'k', '1%')) || ($f = $trklib->get_field_id_from_type($trackerId, 'k', '%,1%')) || ($f =  $trklib->get_field_id_from_type($trackerId, 'k'))) {
				$filterfield[] = $f;
				$filtervalue[] = '';
				$exactvalue[] = $_REQUEST['page'];
			}
		}
			
		if (!isset($filtervalue)) {
			$filtervalue = '';
		} else {
			foreach ($filtervalue as $i=>$f) {
				if ($f == '#user') {
					$filtervalue[$i] = $user;
				}
			}
		}
		
		if (!isset($exactvalue)) {
			$exactvalue = '';
		} else {
			foreach ($exactvalue as $i=>$f) {
				if ($f == '#user') {
					$exactvalue[$i] = $user;
				}
			}
		}
		if (!empty($_REQUEST['itemId']) && (empty($ignoreRequestItemId) || $ignoreRequestItemId != 'y') ) {
			$itemId = $_REQUEST['itemId'];
		}

		if (isset($itemId)) {
			if (strstr($itemId, ':'))
				$itemId = explode(':', $itemId);
			$filter = array('tti.`itemId`'=> $itemId);
		} else {
			$filter = '';
		}
		
		$newItemRateField = false;
		$status_types = $trklib->status_types();
		$smarty->assign('status_types', $status_types);

		if (!isset($filterfield)) {
			$filterfield = '';
		} else {
			if (!empty($filterfield)) {
				if (!empty($filtervalue)) {
					$fvs = $filtervalue;
					unset($filtervalue);
					for ($i = 0, $count_ff = count($filterfield); $i < $count_ff; ++$i) {
						$filtervalue[] = isset($fvs[$i])? $fvs[$i]:'';
					}
				}
				if (!empty($exactvalue)) {
					$evs = $exactvalue;
					unset($exactvalue);
					for ($i = 0, $count_ff2 = count($filterfield); $i < $count_ff2; ++$i) {
						if (isset($evs[$i])) {
							if (is_array($evs[$i])) { // already processed
								$exactvalue[] = $evs[$i];
							} elseif (preg_match('/(not)?categories\(([0-9]+)\)/', $evs[$i], $matches)) {
								global $categlib; include_once('lib/categories/categlib.php');
								$categs = $categlib->list_categs($matches[2]);
								$l = array($matches[2]);
								foreach ($categs as $cat) {
									$l[] = $cat['categId'];
								}
								if (empty($matches[1])) {
									$exactvalue[] = $l;
								} else {
									$exactvalue[] = array('not'=>$l);
								}
							} elseif (preg_match('/(not)?preference\((.*)\)/', $evs[$i], $matches)) {
								if (empty($matches[1])) {
									$exactvalue[] = $prefs[$matches[2]];
								} else {
									$exactvalue[] = array('not'=>$prefs[$matches[2]]);
								}
							} elseif (preg_match('/(not)?field\(([0-9]+)(,([0-9]+|user)(,([0-9]+))?)?\)/', $evs[$i], $matches)) { // syntax field(fieldId, user, trackerId) or field(fieldId)(need the REQUEST['itemId'] or field(fieldId, itemId) or field(fieldId, user)
								if (empty($matches[4]) && !empty($_REQUEST['itemId'])) { // user the itemId of the url
									$matches[4] = $_REQUEST['itemId'];
								}
								if (!empty($matches[4]) && $matches[4] == 'user') {
									if (!empty($matches[6])) { // pick the user item of this tracker
										$t_i = $trklib->get_tracker($matches[6]);
										$matches[4] = $trklib->get_user_item($matches[6], $t_i, $user);
									} elseif ($prefs['userTracker'] == 'y') { //pick the generic user tracker
										global $userlib;
										$utid = $userlib->get_tracker_usergroup($user);
										$matches[4] = $trklib->get_item_id($utid['usersTrackerId'], $utid['usersFieldId'], $user);
									}
								}
								if (!empty($matches[4])) {
									$l = $trklib->get_item_value(0, $matches[4], $matches[2]);
									$field = $trklib->get_tracker_field($matches[2]);
									if ($field['type'] == 'r') {
										$refItemId = $trklib->get_item_id($field['options_array'][0], $field['options_array'][1], $l);
										$l = $trklib->get_item_value($field['options_array'][0], $refItemId, $field['options_array'][3]);
									}
								}
								if (empty($matches[1])) {
									$exactvalue[] = $l;
								} else {
									$exactvalue[] = array('not'=>$l);
								}
							} elseif (preg_match('/(less|greater|lessequal|greaterequal)\((.+)\)/', $evs[$i], $matches)) {
								$conv = array('less'=>'<', 'greater'=>'>', 'lessequal'=>'<=', 'greaterequal'=>'>=');
								$field = $trklib->get_tracker_field($filterfield[$i]);
								if ($field['type'] == 'f' || $field['type'] == 'j') {
									if ($matches[2] == 'now') {
										$matches[2] = $tikilib->now;
									} elseif (($r = strtotime($matches[2])) !== false) {
										$matches[2] = $r;
									}
								}
								$exactvalue[] = array($conv[$matches[1]]=>$matches[2]);
							} elseif (preg_match('/not\((.+)\)/', $evs[$i], $matches)) {
								$exactvalue[] = array('not' => $matches[1]);
							} else {
								$exactvalue[] = $evs[$i];
							}
						} else {
							$exactvalue[] = '';
						}
					}
				}
			}
		}
		if ($tiki_p_admin_trackers != 'y' && $perms['tiki_p_view_trackers'] != 'y' && $tracker_info['writerCanModify'] == 'y' && $user && $userCreatorFieldId) { //patch this should be in list_items
			if ($filterfield != $userCreatorFieldId || (is_array($filterfield) && !in_array($$userCreatorFieldId, $filterfield))) {
				if (is_array($filterfield))
					$filterfield[] = $userCreatorFieldId;
				elseif (empty($filterfield))
					$filterfield = $userCreatorFieldId;
				else
					$filterfield = array($filterfield, $fieldId);
				if (is_array($exactvalue))
					$exactvalue[] = $user;
				elseif (empty($exactvalue))
					$exactvalue = $user;
				else
					$exactvalue = array($exactvalue, $user);
			}
		}
		if ($tiki_p_admin_trackers != 'y' && $perms['tiki_p_view_trackers'] != 'y' && $user && $groupCreatorFieldId) {
			if ($filterfield != $groupCreatorFieldId || (is_array($filterfield) && !in_array($groupCreatorFieldId, $filterfield))) {
				$groups = $userlib->get_user_groups($user);
				if (is_array($filterfield))
					$filterfield[] = $groupCreatorFieldId;
				elseif (empty($filterfield))
					$filterfield = $groupCreatorFieldId;
				else
					$filterfield = array($filterfield, $fieldId);
				if (is_array($exactvalue))
					$exactvalue[] = array_merge($exactvalue, $groups);
				elseif (empty($exactvalue))
					$exactvalue = $groups;
				else
					$exactvalue = array_merge(array($exactvalue), $groups);
				global $group;// awful trick - but the filter garantee that the group is ok
				$smarty->assign_by_ref('ours', $group);
				$perms = array_merge($perms, $trklib->get_special_group_tracker_perm($tracker_info));
			}
		}
		for ($i = 0, $count_allf = count($allfields['data']); $i < $count_allf; $i++) {
			if ($allfields['data'][$i]['type'] == 'C') {
				$infoComputed = $trklib->get_computed_info($allfields['data'][$i]['options_array'][0], $trackerId, $allfields['data']);
				if (!empty($infoComputed)) {
					$allfields['data'][$i] = array_merge($infoComputed, $allfields['data'][$i]);
				}
			} elseif ($allfields["data"][$i]['type'] == 'w') {
				/* keep track of dynamic list items referring to user selectors */
				$refFieldId = $allfields["data"][$i]['options_array'][3];
				$refField = $trklib->get_tracker_field($refFieldId);
				if ($refField['type'] == 'u') {
					$allfields["data"][$i]['type'] = $refField['type'];
				}
			} 
			if ((in_array($allfields["data"][$i]['fieldId'],$listfields) or in_array($allfields["data"][$i]['fieldId'],$popupfields))and $allfields["data"][$i]['isPublic'] == 'y') {
				$passfields["{$allfields["data"][$i]['fieldId']}"] = $allfields["data"][$i];
			}
			if (isset($check['fieldId']) && $allfields["data"][$i]['fieldId'] == $check['fieldId']) {
				$passfields["{$allfields["data"][$i]['fieldId']}"] = $allfields["data"][$i];
				if (!in_array($allfields["data"][$i]['fieldId'], $listfields))
					$allfields["data"][$i]['isPublic'] == 'n'; //don't show it
				$check['ix'] = count($passfields) -1;
			}
			if ($allfields["data"][$i]['name'] == 'page' && empty($filterfield) && empty($displayList)) {
				$filterfield = $allfields["data"][$i]['fieldId'];
				$filtervalue = $_REQUEST['page'];
			}
			if (isset($tracker_info['useRatings']) and $tracker_info['useRatings'] == 'y' 
					and $allfields["data"][$i]['type'] == 's' and $allfields["data"][$i]['name'] == 'Rating') {
				$newItemRateField = $allfields["data"][$i]['fieldId'];
			}
		}
		$smarty->assign_by_ref('filterfield',$filterfield);
		$smarty->assign_by_ref('filtervalue',$filtervalue);
		$smarty->assign_by_ref('fields', $passfields);
		$smarty->assign_by_ref('exactvalue',$exactvalue);
		$smarty->assign_by_ref('listfields', $listfields);
		$smarty->assign_by_ref('popupfields', $popupfields);
		if (!empty($filterfield)) {
			$urlquery['filterfield'] =  is_array($filtervalue) ? implode(':', $filterfield) : $filterfield;
			if (!is_array($filtervalue)) { $filtervalue = array($filtervalue); }
			$urlquery['filtervalue'] = is_array($filtervalue) ? implode(':', $filtervalue) : $filtervalue;
			$urlquery['exactvalue'] = is_array($exactvalue) ? implode(':', $exactvalue) : $exactvalue;
			$urlquery['trackerId'] = $trackerId;
			$smarty->assign('urlquery', $urlquery);
		} else {
			$smarty->assign('urlquery', '');
		}
		if (!empty($export) && $export != 'n' && $tiki_p_export_tracker == 'y') {
			$exportUrl = "tiki-view_tracker.php?trackerId=$trackerId&amp;cookietab=3";
			if (!empty($fields)) {
				$exportUrl .= '&amp;displayedFields='.(is_array($fields)? implode(':', $fields): $fields);
			}
			if (is_array($filterfield)) {
				foreach ($filterfield as $i=>$fieldId) {
					$exportUrl .= "&amp;f_$fieldId=";
					if (empty($filtervalue[$i])) {
						$exportUrl .= $exactvalue[$i];
					} else {
						$exportUrl .= $filtervalue[$i];
					}
				}
			} elseif(!empty($filterfield)) {
				$exportUrl .= "&amp;f_$filterfield=";
				if (empty($filtervalue))
					$exportUrl .= $exactvalue;
				else
					$exportUrl .= $filtervalue;
			}
			$smarty->assign('exportUrl', $exportUrl);
		}

		if (!empty($_REQUEST['delete'])) {
			if (($item_info = $trklib->get_item_info($_REQUEST['delete'])) && $trackerId == $item_info['trackerId']) {
				if ($tiki_p_admin_trackers == 'y'
					|| ($perms['tiki_p_modify_tracker_items'] == 'y' && $item_info['status'] != 'p' && $item_info['status'] != 'c')
					|| ($perms['tiki_p_modify_tracker_items_pending'] == 'y' && $item_info['status'] == 'p')
					|| ($perms['tiki_p_modify_tracker_items_closed'] == 'y' && $item_info['status'] == 'c')	) {
					$trklib->remove_tracker_item($_REQUEST['delete']);
				}
			}
		}
		if (!empty($calendarfielddate)) {
			global $calendarlib; include_once('lib/calendar/calendarlib.php');
			$focusDate = empty($_REQUEST['todate'])? $tikilib->now: $_REQUEST['todate'];
			$focus = $calendarlib->infoDate($focusDate);
			if (!empty($calendardelta)) {
				if ($calendardelta[0] == '-') {
					$focus = $calendarlib->focusPrevious($focus, str_replace('-', '', $calendardelta));
				} else {
					$focus = $calendarlib->focusNext($focus, str_replace('+', '', $calendardelta));
				}
			}
			$calendarlib->focusStartEnd($focus, $calendarviewmode, $calendarbeginmonth, $startPeriod, $startNextPeriod);
			$cell = $calendarlib->getTableViewCells($startPeriod, $startNextPeriod, $calendarviewmode, $calendarlib->firstDayofWeek($user));
			$filterfield[] = $calendarfielddate[0];
			$filtervalue[] = '';
			$exactvalue[] = array('>=' => $startPeriod['date']);
			$filterfield[] = empty($calendarfielddate[1])?$calendarfielddate[0]: $calendarfielddate[1];
			$filtervalue[] = '';
			$exactvalue[] = array('<' => $startNextPeriod['date']);
		}

		if (count($passfields)) {
			// Optimization: Group category fields using AND logic indicated by sub-array
			$catfilters = array();
			$catfiltervalue = array();
			$catfilternotvalue = array();
			if (!empty($filterfield)) {
			foreach ($filterfield as $k => $ff) {
				$filterfieldinfo = $trklib->get_tracker_field($ff);
				if ($filterfieldinfo['type'] == 'e') {
					$catfilters[] = $k;
					if (!empty($filtervalue[$k]) && empty($exactvalue[$k]) ) {
						// Some people use filtervalue instead of exactvalue for category filters
						$exactvalue[$k] = $filtervalue[$k];
						for ($i = 0; $i < $k; $i++) {
							if (!isset($exactvalue[$i])) {
								$exactvalue[$i] = '';
							}
						} 
					} 
					if (array_key_exists('not', $exactvalue[$k])) {
						$catfilternotfield[0] = $ff;
						$catfilternotvalue[] = array($exactvalue[$k]);
					} else {
						$catfilterfield[0] = $ff;
						$catfiltervalue[] = array($exactvalue[$k]);
					}
				}
			}
			}
			if ($catfilters) {
				foreach ($catfilters as $cf) {
					unset($filterfield[$cf]);
					unset($exactvalue[$cf]);
				}
				if ($catfiltervalue) {
					// array_merge is used because it reindexes
					$filterfield = array_merge($filterfield, $catfilterfield);
					$exactvalue = array_merge($exactvalue, array($catfiltervalue));
				}
				if ($catfilternotvalue) {
					$filterfield = array_merge($filterfield, $catfilternotfield);
					$exactvalue[] = array('not' => $catfilternotvalue);
				}
			}
			// End Optimization
			$items = $trklib->list_items($trackerId, $tr_offset, $max, $tr_sort_mode, $passfields, $filterfield, $filtervalue, $tr_status, $tr_initial, $exactvalue, $filter, $allfields);
			if (isset($silent) && $silent == 'y' && empty($items['cant'])) {
				return;
			}

			if ($items['cant'] == 1 && isset($goIfOne) && ($goIfOne == 'y' || $goIfOne == 1)) {
				header('Location: tiki-view_tracker_item.php?itemId='.$items['data'][0]['itemId'].'&amp;trackerId='.$items['data'][0]['trackerId']);
				die;
			}
			
			if ($newItemRateField && !empty($items['data'])) {
				foreach ($items['data'] as $f=>$v) {
					$items['data'][$f]['my_rate'] = $tikilib->get_user_vote("tracker.".$trackerId.'.'.$items['data'][$f]['itemId'],$user);
				}
			}
			if ($tracker_info['useComments'] == 'y' && $tracker_info['showComments'] == 'y') {
				foreach ($items['data'] as $itkey=>$oneitem) {
					$items['data'][$itkey]['comments'] = $trklib->get_item_nb_comments($items['data'][$itkey]['itemId']);
				}
			}
			if ($tracker_info['useAttachments'] == 'y' && $tracker_info['showAttachments'] == 'y') {
				foreach ($items["data"] as $itkey=>$oneitem) {
					$res = $trklib->get_item_nb_attachments($items["data"][$itkey]['itemId']);
					$items["data"][$itkey]['attachments']  = $res['attachments'];
				}
			}
			if (!empty($compute) && !empty($items['data'])) {
				$fs = preg_split('/ *: */', $compute);
				foreach ($fs as $fieldId) {
					if (strstr($fieldId, "/")) {
						list($fieldId, $oper) = preg_split('/ *\/ */', $fieldId);
						$oper = strtolower($oper);
						if ($oper == 'average') {
							$oper = 'avg';
						} elseif ($oper != 'sum' && $oper != 'avg') {
							$oper = 'sum';
						}
					} else {
						$oper = 'sum';
					}
					foreach ($items['data'] as $i=>$item) {
						foreach ($item['field_values'] as $field) {
							if ($field['fieldId'] == $fieldId) {
								if (preg_match('/^ *$/', $field['value']) || !is_numeric($field['value']))
									$amount[$i] = '0';
								else
									$amount[$i] = $field['value'];
								break;
							}
						}
					}						
					eval('$value='.implode('+', $amount).';');
					if ($oper == 'avg')
						$value = round($value / count($amount));
					$computedFields[$fieldId][] = array_merge(array('computedtype' => 'n', 'operator'=>$oper, 'value'=>$value), $passfields[$fieldId]);
				}
				$smarty->assign_by_ref('computedFields', $computedFields);
			} else {
				$smarty->assign('computedFields', '');
			}
			if (!empty($calendarfielddate)) {
				foreach ($items['data'] as $i => $item) {
					if (!empty($wiki)) {
						$smarty->assign('fields', $item['field_values']);
						$smarty->assign('item', $item);
						$smarty->assign('wiki', "wiki:$wiki");
						$smarty->assign('showpopup', 'n');
						$items['data'][$i]['over'] = $smarty->fetch('tracker_pretty_item.tpl');
					}
					if (!empty($tplwiki)) {
						$smarty->assign('fields', $item['field_values']);
						$smarty->assign('item', $item);
						$smarty->assign('wiki', "tplwiki:$tplwiki");
						$smarty->assign('showpopup', 'n');
						$items['data'][$i]['over'] = $smarty->fetch('tracker_pretty_item.tpl');
					}
					if (empty($items['data'][$i]['over'])) {
						$items['data'][$i]['over'] = $trklib->get_isMain_value($trackerId, $item['itemId']);
					}
					$items['data'][$i]['visible'] = 'y';
				}
				$trklib->fillTableViewCell($items['data'], $calendarfielddate, $cell);
				$smarty->assign('cell', $cell);
				$smarty->assign('show_calendar_module', 'y');
				$calendarlib->getDayNames($calendarlib->firstDayofWeek($user), $daysnames, $daysnames_abr);
				$smarty->assign('daysnames_abr', $daysnames_abr);
				$smarty->assign('focusmonth', TikiLib::date_format("%m", $focusDate));
				$smarty->assign('module_params', array('viewmode'=>'n', 'showaction'=>'n', 'notitle'=>empty($calendartitle)?'y':'n', 'title'=>$calendartitle, 'viewnavbar' => $calendarviewnavbar, 'decorations'=> empty($calendartitle)?'n':'y'));
				$smarty->assign('tpl_module_title', tra($calendartitle));
				$smarty->assign('now', $tikilib->now);
				$smarty->assign('calendarViewMode', $calendarviewmode);
				$smarty->assign('viewmodelink', $calendarviewmode);
				$smarty->assign('viewmode', $calendarviewmode);
				$focus_prev = $calendarlib->focusPrevious($focus, $calendarviewmode);
				$smarty->assign('focus_prev', $focus_prev['date']);
				$focus_next = $calendarlib->focusNext($focus, $calendarviewmode);
				$smarty->assign('focus_next', $focus_next['date']);
				$smarty->assign('daystart', $startPeriod['date']);
				$dayend =  $calendarlib->infoDate($startNextPeriod['date']-1);
				$smarty->assign('dayend', $dayend['date']);
				$smarty->assign('today', TikiLib::make_time(0,0,0, TikiLib::date_format('%m'), TikiLib::date_format('%d'), TikiLib::date_format('%Y')));
				$smarty->assign('sticky_popup', $calendarstickypopup);
				$smarty->assign('showpopup', 'n');
				global $headerlib; include_once('lib/headerlib.php');
				$headerlib->add_cssfile('css/calendar.css',20);
				return '~np~'.$smarty->fetch('modules/mod-calendar_new.tpl').'~/np~';
			}
			if (!empty($wiki)) {
				$tpl = "wiki:$wiki";
			} elseif (!empty($tplwiki)) {
				$tpl = "tplwiki:$tplwiki";
			} elseif (empty($tpl)) {
				$tpl = '';
			}
			if (!empty($tpl))
				$smarty->security = true;
			$smarty->assign('tpl', $tpl);
			
			if (!empty($itemId) && $showpagination == 'y' && !empty($_REQUEST['cant'])) {
				$smarty->assign('max', 1);
				$smarty->assign('count_item', $_REQUEST['cant']);
				$smarty->assign('offset_arg', 'reloff');
				$smarty->assign('tr_offset', $_REQUEST['reloff']);
			} else {
				$smarty->assign_by_ref('max', $max);
				$smarty->assign_by_ref('item_count', $items['cant']);
				$smarty->assign_by_ref('count_item', $items['cant']);
				$smarty->assign('offset_arg', 'tr_offset');
			}
			$smarty->assign_by_ref('items', $items["data"]);
			$smarty->assign('daformat', $tikilib->get_long_date_format()." ".tra("at")." %H:%M"); 
			
			if (!empty($params['googlemap']) && $params['googlemap'] == 'y') {
				$smarty->assign('trackerlistmapview', true);
				$smarty->assign('trackerlistmapname', "trackerlistgmap_$iTRACKERLIST");
				// Check for custom bubble text
				$unlimitedallfields = $trklib->list_tracker_fields($trackerId);
				$markerfields = array();
				foreach ($unlimitedallfields["data"] as $f) {
					if ($f["type"] == 'G' && $f["options_array"][0] == 'y' && !empty($f["options_array"][1])) {
						$markerfields = explode('|', $f["options_array"][1]);
						break;
					}
				}
				// Generate Google map plugin data
				if (!empty($params["googlemapicon"])) {
					$googlemapicon = $params["googlemapicon"];
				} else {
					$googlemapicon = '';
				}
				global $gmapobjectarray;
				$gmapobjectarray = array();
				foreach ($items["data"] as $i) {
					if (!empty($params["url"])) {
						$href = str_replace('itemId', $i["itemId"], $params["url"]);
					} else {
						$href = 'tiki-view_tracker_item.php?itemId=' . $i["itemId"];
					}
					$markertext = '';
					$markertitle = $i["value"];
					foreach ($markerfields as $k => $m) {
						foreach ($i["field_values"] as $f) {
							if ($f["fieldId"] == $m) {								
								if ($k == 0 && !empty($f["value"])) {
									$markertitle = preg_replace("/[\r\n|\r|\n]/", "<br />", htmlspecialchars($f["value"]));
								} elseif (!empty($f["value"])) {
									if ($markertext) {
										$markertext .= '<br /><br />';
									}
									$markertext .= preg_replace("/[\r\n|\r|\n]/", "<br />", htmlspecialchars($f["value"]));
								}
								break;
							}
						}
					}
					
					$gmapobjectarray[] = array('type' => 'trackeritem',
						'id' => $i["itemId"],
						'title' => $markertitle,
						'href' => $href,
						'icon' => $googlemapicon,
						'text' => $markertext,
					);
				}
			} else {
				$smarty->assign('trackerlistmapview', false);
			}

			$tracker = $tikilib->get_tracker($trackerId,0,-1);
			/*foreach ($query_array as $k=>$v) {
				if (!is_array($v)) { //only to avoid an error: eliminate the params that are not simple (ex: if you have in the same page a tracker list plugin and a tracker plugin, filling the tracker plugin interfers with the tracker list. In any case this is buggy if two tracker list plugins in the same page and if one needs the query value....
					$quarray[] = urlencode($k) ."=". urlencode($v);
				}
			}
			if (is_array($quarray)) {
				$query_string = implode("&amp;",$quarray);
			} else {
				$quering_string = '';
			}
			$smarty->assign('query_string', $query_string);
			*/
			if (!$tracker) {
				$smarty->assign('msg', tra("Error in tracker ID"));
				return "~np~".$smarty->fetch("error_simple.tpl")."~/np~";
			} else {
				$save_fc = null;
				if (!empty($wiki) && $params['force_compile'] === 'y') { // some pretty trackers need to compile fresh for each item
					$save_fc = $smarty->force_compile;
					$smarty->force_compile = true;
				}
				
				if (!empty($displaysheet) && $displaysheet == 'y') {
					global $headerlib;
					$headerlib->add_jq_onready('
						if (typeof ajaxLoadingShow == "function") {
							ajaxLoadingShow("role_main");
						}
						setTimeout (function () {
							$("div.trackercontainer").tiki("sheet", "",{
								editable:false,
								buildSheet: true,
								minSize: {rows: 0, cols: 0}
							});
						}, 0);', 500);
					$smarty->assign('displaysheet', 'true');
				}
				
				$str = $smarty->fetch('wiki-plugins/wikiplugin_trackerlist.tpl');
				if ($save_fc !== null) {
					$smarty->force_compile = $save_fc;	// presumably will be false but put it back anyway
				}
				
				return "~np~".$str."~/np~";
			}
		} else {
			$smarty->assign('msg', tra("No field indicated"));
			return "~np~".$smarty->fetch("error_simple.tpl")."~/np~";
		}
	}
	return $back;
}
