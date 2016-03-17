<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function wikiplugin_trackerlist_info()
{
	$ts = new Table_Plugin;
	$ts->createParams();
	$tsparams = $ts->params;
	$params = array_merge(
		array(
			 'trackerId' => array(
				 'required' => true,
				 'name' => tra('Tracker ID'),
				 'description' => tra('Numeric value representing the tracker ID'),
				 'since' => '1',
				 'filter' => 'digits',
				 'default' => '',
				 'profile_reference' => 'tracker',
			 ),
			 'fields' => array(
				 'required' => false,
				 'name' => tra('Fields'),
				 'description' => tr('Colon-separated list of field IDs for the fields to be displayed.
					Example: %02:4:5%1. The field order specified here determines the column order if the %0sort%1
					parameter is set to %0y%1.', '<code>', '</code>'),
				 'since' => '1',
				 'filter' => 'digits',
				 'separator' => ':',
				 'default' => '',
				 'profile_reference' => 'tracker_field',
			 ),
			 'sort' => array(
				 'required' => false,
				 'name' => tra('Sort'),
				 'description' => tr('Display columns in the order listed in the %0fields%1 parameter instead of by
					field ID (field ID order is used by default', '<code>', '</code>'),
				 'since' => '2.0',
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
				 'description' => tr('Colon-separated list of fields which will display in a tooltip on mouse over.
					Example: %06:7%1', '<code>', '</code>'),
				 'since' => '2.0',
				 'filter' => 'digits',
				 'separator' => ':',
				 'default' => '',
			 ),
			 'stickypopup' => array(
				 'required' => false,
				 'name' => tra('Sticky Popup'),
				 'description' => tra('Choose whether the popup tooltip will stay displayed on mouse out (does not stay open by default)'),
				 'since' => '2.0',
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
				 'since' => '1',
				 'doctype' => 'show',
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
				 'description' => tra('Show links to each tracker item (not shown by default). At least one field needs
					to be set as Public in order for this to work.'),
				 'since' => '1',
				 'doctype' => 'show',
				 'filter' => 'alpha',
				 'default' => 'n',
				 'options' => array(
					 array('text' => '', 'value' => ''),
					 array('text' => tra('Yes'), 'value' => 'y'),
					 array('text' => tra('No'), 'value' => 'n'),
					 array('text' => tra('Row'), 'value' => 'r')
				 )
			 ),
			 'showdesc' => array(
				 'required' => false,
				 'name' => tra('Show Description'),
				 'description' => tra('Show the tracker\'s description (not shown by default)'),
				 'since' => '1',
				 'doctype' => 'show',
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
				 'since' => '2.0',
				 'doctype' => 'show',
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
				 'description' => tra('Show an alphabetical index by first letter to assist in navigation (not shown
					by default)'),
				 'since' => '1',
				 'doctype' => 'show',
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
				 'since' => '1',
				 'doctype' => 'show',
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
				 'description' => tra('Creation date display is based on tracker settings unless overridden here'),
				 'since' => '2.0',
				 'doctype' => 'show',
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
				 'description' => tra('Last modification date display is based on tracker settings unless overridden here'),
				 'since' => '2.0',
				 'doctype' => 'show',
				 'filter' => 'alpha',
				 'default' => '',
				 'options' => array(
					 array('text' => '', 'value' => ''),
					 array('text' => tra('Yes'), 'value' => 'y'),
					 array('text' => tra('No'), 'value' => 'n')
				 )
			 ),
			 'showlastmodifby' => array(
				 'required' => false,
				 'name' => tra('Last modified by'),
				 'description' => tra('Last modified by user display is based on tracker settings unless overridden here'),
				 'since' => '14.0',
				 'doctype' => 'show',
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
				 'since' => '1',
				 'doctype' => 'show',
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
				 'since' => '4.0',
				 'doctype' => 'show',
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
				 'since' => '1',
				 'doctype' => 'filter',
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
				 'description' => tra('Sort rows in ascending (_asc) or descending (_desc) order based on field ID,
					date created or date last modified'),
				 'accepted' => 'created_asc, created_desc, lastModif_asc, lastModif_desc, f_fieldId_asc, f_filedId_desc '
					 . tr('(replacing %0fieldId%1 with the field ID number, e.g. %0f_3_asc%1)', '<code>', '</code>'),
				 'since' => '1',
				 'filter' => 'word',
				 'default' => '',
			 ),
			 'sortchoice' => array(
				 'required' => false,
				 'name' => tra('Sort Choice'),
				 'description' => tr('Add a dropdown of sorting choices. Separate each choice with a %0:%1. For each
					choice, use the format %0value|label%1. See %0sort_mode%1 for value choices. Example with two
					sorting choices: %0sortchoice="created_desc|Newest first:lastModif_desc|Last modified first"%1',
					'<code>', '</code>'),
				 'since' => '5.0',
				 'filter' => 'text',
				 'separator' => ':',
				 'default' => '',
			 ),
			 'max' => array(
				 'required' => false,
				 'name' => tra('Maximum Items'),
				 'description' => tra('Maximum number of items to display or -1 for all items. Defaults to max records
					preference, if set. Pagination will not show if all items are shown by setting to -1.'),
				 'since' => '1',
				 'doctype' => 'filter',
				 'filter' => 'int',
				 'default' => '',
			 ),
			 'offset' => array(
				 'required' => false,
				 'name' => tra('Offset'),
				 'description' => tra('Offset of first item. Default is no offset.'),
				 'since' => '6.0',
				 'doctype' => 'filter',
				 'filter' => 'int',
				 'default' => 0,
			 ),
			 'forceoffset' => array(
				 'required' => false,
				 'name' => tra('Fix offset always (no pagination)'),
				 'description' => tra('Fix offset to that specified. This will disallow pagination.'),
				 'since' => '8.0',
				 'doctype' => 'filter',
				 'filter' => 'alpha',
				 'default' => 'n',
				 'options' => array(
					 array('text' => '', 'value' => ''),
					 array('text' => tra('Yes'), 'value' => 'y'),
					 array('text' => tra('No'), 'value' => 'n')
				 )
			 ),
			 'showpagination' => array(
				 'required' => false,
				 'name' => tra('Show Pagination'),
				 'description' => tra('Determines whether pagination will be shown (shown by default)'),
				 'since' => '4.0',
				 'doctype' => 'show',
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
				 'since' => '1',
				 'doctype' => 'filter',
				 'filter' => 'digits',
				 'separator' => ':',
				 'default' => '',
				 'profile_reference' => 'tracker_field',
			 ),
			 'filtervalue' => array(
				 'required' => false,
				 'name' => tra('Filter Value'),
				 'description' => tr('Filter value (or multiple values) that correspond to %0filterfield%1. For better performance, use %0exactvalue%1
					instead. %2Special search values to match:', '<code>', '</code>', '<br>') . '<br>'
					. '<code>*value</code> - ' . tra('text that ends in "value"') . '<br>'
					. '<code>value*</code> - ' . tra('text that begins with "value"') . '<br>'
					. '<code>#user</code> - ' . tra('the current user\'s login name') . '<br>'
					. '<code>#group_default</code> - ' . tra('the current user\'s default group') . '<br>',
				 'since' => '1',
				 'doctype' => 'filter',
				 'accepted' => tra('any text'),
				 'filter' => 'text',
				 'separator' => ':',
				 'default' => '',
			 ),
			 'exactvalue' => array(
				 'required' => false,
				 'name' => tra('Exact Value'),
				 'description' => tr('Exact value (or multiple values) that correspond to %0filterfield%1. %2Special
					search values to filter by:', '<code>', '</code>', '<br>') . '<br>'
					 . '<code>categories(x)</code> - ' . tra('tracker item is in category with ID x or one its descendants') . '<br>'
					 . '<code>notcategories(x)</code> - ' . tra('tracker item is not in category with ID x or one of its descendants') . '<br>'
					 . '<code>preference(name)</code> - ' . tra('match against the value of a Tiki preference') . '<br>'
					 . '<code>notpreference(name)</code> - ' . tra('match if value does not equal a Tiki preference value') . '<br>'
					 . '<code>not(value)</code> - ' . tra('match if the field does not equal "value"') . '<br>'
					 . '<code>field(x, itemid)</code> - ' . tr('match field with ID x in item with ID itemid.
						%0field(x)%1 can be used if the %0itemId%1 URL parameter is set', '<code>', '</code>') . '<br>'
					 . '<code>notfield(x, itemid)</code> - ' . tr('match if not equal to field with ID x in item with ID itemid
						%0field(x)%1 can be used if the %0itemId%1 URL parameter is set', '<code>', '</code>') . '<br>'
					 . tr('The following comparisons can also be applied to date fields by using date phrases that PHP recognizes
						(see http://us.php.net/manual/en/function.strtotime.php ):', '<code>', '</code>') . '<br>'
					 . '<code>less(value)</code> - ' . tra('match if less than "value"') . '<br>'
					 . '<code>greater(value)</code> - ' . tra('match if greater than "value"') . '<br>'
					 . '<code>lessequal(value)</code> - ' . tra('match if less than or equal to "value"') . '<br>'
					 . '<code>greaterequal(value)</code> - ' . tra('match if greater than or equal to "value"') . '<br>',
				 'since' => '1',
				 'doctype' => 'filter',
				 'accepted' => tra('any text'),
				 'filter' => 'text',
				 'separator' => ':',
				 'default' => '',
			 ),
			 'checkbox' => array(
				 'required' => false,
				 'name' => tra('Checkbox'),
				 'description' => tr('Adds a checkbox on each line to perform an action. Required elements are separated
					by %0/%1. Those elements are:', '<code>', '</code>') . '<br />'
					. '<code>FieldId</code> - ' . tra('the value of this field will be posted to the action') . '<br>'
					. '<code>PostName</code> - ' . tra('the name of the post') . '<br>'
					. '<code>Title</code> - ' . tra('the title of the submit button') . '<br>'
					. '<code>Submit</code> - ' . tra('the name of the submit button') . '<br>'
					. '<code>ActionUrl</code> - ' . tra('the file that will be called upon submit') . '<br>'
					. '<code>Tpl</code> - ' . tra('optional template inserted before the submit button and returned') . '<br>'
					. '<code>SelectType</code> - ' . tr('Leave empty for multiple select, or use %0dropdown%1 or
						%0radio%1.', '<code>', '</code>') . '<br>'
					. tr('Format: %0checkbox="FieldId/PostName/Title/Submit/ActionUrl/Tpl/dropdown"%1', '<code>',
						'</code>') . '<br />'
					 . tr('Example: %0checkbox="6/to/Email to selected/submit/messu-compose.php//dropdown"%1', '<code>',
						 '</code>') . '<br />',
				 'since' => '1',
				 'doctype' => 'show',
				 'advanced' => true,
				 'default' => '',
			 ),
			 'goIfOne' => array(
				 'required' => false,
				 'name' => tra('goIfOne'),
				 'description' => tra('Display the item rather than list if only one item is found'),
				 'since' => '1',
				 'doctype' => 'filter',
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
				 'description' => tra('Show a \'more\' button that links to the tracker item (not shown by default)'),
				 'since' => '2.0',
				 'doctype' => 'show',
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
				 'since' => '2.0',
				 'doctype' => 'show',
				 'filter' => 'url',
				 'default' => 'tiki-view_tracker.php',
			 ),
			 'view' => array(
				 'required' => false,
				 'name' => tra('View'),
				 'description' => tr('Display only the items of the following:') . '<br />'
					. '<code>user</code> - ' . tra('the current user') . '<br>'
					. '<code>page</code> - ' . tra('the current page name') . '<br>'
					. '<code>ip</code> - ' . tra('the current IP address') . '<br>',
				 'since' => '2.0',
				 'doctype' => 'filter',
				 'filter' => 'alpha',
				 'advanced' => true,
				 'default' => '',
				 'options' => array(
					 array('text' => '', 'value' => ''),
					 array('text' => tra('Page'), 'value' => 'page'),
					 array('text' => tra('User'), 'value' => 'user'),
					 array('text' => tra('IP address'), 'value' => 'ip')
				 )
			 ),
			 'tpl' => array(
				 'required' => false,
				 'name' => tra('Template File'),
				 'description' => tr('Use content of the specified tpl file as template to display the item.
					Use %0{$f_fieldId}%1 to display a field with ID %0fieldId%1.', '<code>', '</code>'),
				 'since' => '2.0',
				 'advanced' => true,
				 'default' => '',
			 ),
			 'wiki' => array(
				 'required' => false,
				 'name' => tra('Wiki Page'),
				 'description' => tr('Use content of the wiki page as template to display the item. The page should
					have the permission %0tiki_p_use_as_template%1 set, and should only be editable by trusted users
					such as other site admins', '<code>', '</code>'),
				 'since' => '2.0',
				 'filter' => 'pagename',
				 'advanced' => true,
				 'default' => '',
				 'profile_reference' => 'wiki_page',
			 ),
			 'tplwiki' => array(
				 'required' => false,
				 'name' => tra('Template Wiki Page'),
				 'description' => tr('Use content of the wiki page as template to display the item but with as little
					parsing on the content as with a tpl on disk. The page should have the permission
					%0tiki_p_use_as_template%1 set, and should only be editable by trusted users such as other site
					admins', '<code>', '</code>'),
				 'since' => '6.5 & 7.1',
				 'filter' => 'pagename',
				 'advanced' => true,
				 'default' => '',
				 'profile_reference' => 'wiki_page',
			 ),
			 'view_user' => array(
				 'required' => false,
				 'name' => tra('View User'),
				 'description' => tra('Will display the items of the specified user'),
				 'since' => '2.0',
				 'doctype' => 'filter',
				 'default' => '',
			 ),
			 'itemId' => array(
				 'required' => false,
				 'name' => tra('Item ID'),
				 'description' => tra('Colon-separated list of item IDs to restrict the listing to'),
				 'since' => '2.0, multiple since 3.0',
				 'doctype' => 'filter',
				 'filter' => 'digits',
				 'separator' => ':',
				 'default' => '',
				 'profile_reference' => 'tracker_item',
			 ),
			 'ignoreRequestItemId' => array(
				 'required' => false,
				 'name' => tra('Ignore ItemId'),
				 'description' => tra('Ignore the itemId url parameter when filtering list (not ignored by default)'),
				 'since' => '5.0',
				 'doctype' => 'filter',
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
				 'description' => tr('The link that will be on each main field when %0showlinks="y"%1. Special values:',
					'<code>', '</code>') . '<br>'
					. '<code>url="mypage?<strong>itemId</strong>"</code> - '
						. tra('will link to the item based on its item ID') . '<br>'
					. '<code>url="mypage?<strong>tr_offset</strong>"</code> - '
						. tra('will link to the item based on its offset value') . '<br>'
					. '<code>url="<strong>sefurl</strong>"</code> - '
						. tr('will link to the item using %0itemX%1 (where X is the item ID) for when SEFURL is being
						used', '<code>', '</code>') . '<br>'
					. '<code><strong>vi_tpl</strong></code> - ' . tr('use to show the item without admin buttons and with a back button
						when using a template (Display > Section Format must be set to "Configured" in the tracker
						properties). Example:', '<code>', '</code>')
						. ' <code>url="tiki-view_tracker_item.php?<strong>vi_tpl</strong>=wiki:PageName&itemId"</code><br>'
					. '<code><strong>ei_tpl</strong></code> - ' . tr('similar to %0vi_tpl%1 except that admin buttons are shown for users
						with proper permissions when "Restrict non admins to wiki page access only" is set in the
						tracker properties.', '<code>', '</code>') . '<br>',
				 'since' => tr('2.0, 3.0 for %0itemId%1, 11.0 for %0tr_offset%1, 14.0 for %0sefurl%1 and %0vi_tpl%1',
					 '<code>', '</code>'),
				 'doctype' => 'show',
				 'parent' => array('name' => 'showlinks', 'value' => 'y'),
				 'filter' => 'url',
				 'default' => '',
			 ),
			 'ldelim' => array(
				 'required' => false,
				 'name' => tra('Left Delimiter'),
				 'description' => tra('Smarty left delimiter for Latex generation. Example:') . '<code>@{</code>',
				 'since' => '2.0',
				 'advanced' => true,
				 'default' => '{',
			 ),
			 'rdelim' => array(
				 'required' => false,
				 'name' => tra('Right Delimiter'),
				 'description' => tra('Smarty right delimiter for Latex generation Example:') . '<code>}@</code>',
				 'since' => '2.0',
				 'advanced' => true,
				 'default' => '}',
			 ),
			 'list_mode' => array(
				 'required' => false,
				 'name' => tra('List Mode'),
				 'description' => tra('Set output format. Yes (y) displays tracker list view with truncated values
					(default); No (n) displays in tracker item view; Comma Separated Values (csv) outputs without any
					HTML formatting.'
				 ),
				 'since' => '3.0',
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
				 'since' => '3.0',
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
				 'description' => tra('Sum or average all the values of a field and displays it at the bottom of the
					table.').' <code>'.tra('fieldId').'/sum:'.tra('fieldId').'/avg</code>',
				 'since' => '3.0',
				 'filter' => 'text',
				 'accepted' => tr('%0, separated by %1', '<code>fieldId/operator</code>', '<code>:</code>'),
				 'advanced' => true,
				 'default' => '',
				 'profile_reference' => 'tracker_field_string',
			 ),
			 'silent' => array(
				 'required' => false,
				 'name' => tra('Silent'),
				 'description' => tra('Show nothing if no items found (the table header and a \'No records found\'
				    message is shown by default).'),
				 'since' => '4.0',
				 'doctype' => 'show',
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
				 'description' => tra('Show a delete icon for each item (not shown by default)'),
				 'since' => '4.0',
				 'doctype' => 'show',
				 'filter' => 'alpha',
				 'default' => 'n',
				 'options' => array(
					 array('text' => '', 'value' => ''),
					 array('text' => tra('Yes'), 'value' => 'y'),
					 array('text' => tra('No'), 'value' => 'n')
				 )
			 ),
			 'urlafterdelete' => array(
				 'required' => false,
				 'name' => tra('Url to redirect to after delete'),
				 'description' => tra('Url to redirect to after delete'),
				 'since' => '11.0',
				 'filter' => 'url',
				 'default' => '',
			 ),
			 'showopenitem' => array(
				 'required' => false,
				 'name' => tra('Show Open Item'),
				 'description' => tra('Show an open item  option (not shown by default)'),
				 'since' => '8.0',
				 'doctype' => 'show',
				 'filter' => 'alpha',
				 'default' => 'n',
				 'options' => array(
					 array('text' => '', 'value' => ''),
					 array('text' => tra('Yes'), 'value' => 'y'),
					 array('text' => tra('No'), 'value' => 'n')
				 )
			 ),
			 'showcloseitem' => array(
				 'required' => false,
				 'name' => tra('Show Close Item'),
				 'description' => tra('Show a close item option (not shown by default)'),
				 'since' => '8.0',
				 'doctype' => 'show',
				 'filter' => 'alpha',
				 'default' => 'n',
				 'options' => array(
					 array('text' => '', 'value' => ''),
					 array('text' => tra('Yes'), 'value' => 'y'),
					 array('text' => tra('No'), 'value' => 'n')
				 )
			 ),
			 'showpenditem' => array(
				 'required' => false,
				 'name' => tra('Show Pending Item'),
				 'description' => tra('Show a pending item option (not shown by default)'),
				 'since' => '8.0',
				 'doctype' => 'show',
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
				 'since' => '5.0',
				 'doctype' => 'show',
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
				 'since' => '5.0',
				 'doctype' => 'show',
				 'filter' => 'alpha',
				 'default' => 'n',
				 'options' => array(
					 array('text' => '', 'value' => ''),
					 array('text' => tra('Yes'), 'value' => 'y'),
					 array('text' => tra('No'), 'value' => 'n')
				 )
			 ),
			 'showmap' => array(
				 'required' => false,
				 'name' => tra('Show Results Map'),
				 'description' => tra('Show Map of results (not shown by default)'),
				 'since' => '12.0',
				 'doctype' => 'show',
				 'filter' => 'alpha',
				 'default' => '',
				 'options' => array(
					 array('text' => '', 'value' => ''),
					 array('text' => tra('Yes'), 'value' => 'y'),
					 array('text' => tra('No'), 'value' => 'n')
				 )
			 ),
			 'calendarfielddate' => array(
				 'required' => false,
				 'name' => tra('Calendar Field IDs'),
				 'description' => tr('Used to display items in a calendar view. One fieldId if one date, or 2 fieldIds
					separated with %0:%1 for %0start:end%1', '<code>', '</code>'),
				 'since' => '6.0',
				 'doctype' => 'calendar',
				 'separator' => ':',
				 'filter' => 'digits',
				 'default' => '',
				 'profile_reference' => 'tracker_field',
			 ),
			 'calendarviewmode' => array(
				 'required' => false,
				 'name' => tra('Calendar View Mode'),
				 'description' => tra('Calendar view type time span (default is month)'),
				 'since' => '6.0',
				 'doctype' => 'calendar',
				 'filter' => 'word',
				 'default' => 'month',
				 'options' => array(
					 array('text' => '', 'value' => ''),
					 array('text' => tra('Month'), 'value' => 'month'),
					 array('text' => tra('Two months'), 'value' => 'bimester'),
					 array('text' => tra('Trimester'), 'value' => 'trimester'),
					 array('text' => tra('Quarter'), 'value' => 'quarter'),
					 array('text' => tra('Semester'), 'value' => 'semester'),
					 array('text' => tra('Year'), 'value' => 'year')
				 )
			 ),
			 'calendarpopup' => array(
				 'required' => false,
				 'name' => tra('Calendar Popup'),
				 'description' => tr('Calendar items will popup, overrides the %0stickypopup%1 parameter if turned off
					(default is to pop up).', '<code>', '</code>'),
				 'since' => '6.0',
				 'doctype' => 'calendar',
				 'filter' => 'alpha',
				 'default' => 'y',
				 'options' => array(
					 array('text' => '', 'value' => ''),
					 array('text' => tra('Yes'), 'value' => 'y'),
					 array('text' => tra('No'), 'value' => 'n')
				 ),
			 ),
			 'calendarstickypopup' => array(
				 'required' => false,
				 'name' => tra('Sticky Popup'),
				 'description' => tra('Calendar item popups will stay open if set to y (Yes). Not sticky by default'),
				 'since' => '6.0',
				 'doctype' => 'calendar',
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
				 'since' => '6.0',
				 'doctype' => 'calendar',
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
				 'since' => '6.0',
				 'doctype' => 'calendar',
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
				 'since' => '6.0',
				 'doctype' => 'calendar',
				 'filter' => 'text',
				 'default' => '',
			 ),
			 'calendardelta' => array(
				 'required' => false,
				 'name' => tra('Calendar Delta'),
				 'description' => tra('Set the calendar delta that will be shown (not set by default)'),
				 'since' => '6.0',
				 'doctype' => 'calendar',
				 'filter' => 'text',
				 'default' => '',
				 'options' => array(
					 array('text' => '', 'value' => ''),
					 array('text' => tra('Plus Month'), 'value' => '+month'),
					 array('text' => tra('Minus Month'), 'value' => '-month'),
					 array('text' => tra('Plus Two months'), 'value' => '+bimester'),
					 array('text' => tra('Minus Two months'), 'value' => '-bimester')
				 )
			 ),
			 'displaysheet' => array(
				 'required' => false,
				 'name' => tra('Display Spreadsheet.'),
				 'description' => tra('Display tracker as a spreadsheet (not used by default)'),
				 'since' => '6.0',
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
				 'description' => tra('Force Smarty to recompile the templates for each tracker item when using a
				    wiki page as a template. Default=n (best performance)'),
				 'since' => '6.0',
				 'filter' => 'alpha',
				 'default' => 'n',
				 'advanced' => true,
				 'options' => array(
					 array('text' => '', 'value' => ''),
					 array('text' => tra('Yes'), 'value' => 'y'),
					 array('text' => tra('No'), 'value' => 'n')
				 )
			 ),
			 'periodQuantity' => array(
				 'required' => false,
				 'name' => tr('Period quantity'),
				 'description' => tr('Numeric value to display only last tracker items created within a user defined
				    time-frame. Used in conjunction with the next parameter "Period unit", this parameter indicates how
				    many of those units are to be considered to define the time frame. Use in conjunction with
				    %0max=-"1"%1 to list all items (by default %0max%1 is set to %010%1).', '<code>', '</code>'),
				 'since' => '6.5, <s>7.x</s> & 8.0',
				 'doctype' => 'filter',
				 'filter' => 'int',
				 'default' => '',
			 ),
			 'periodUnit' => array(
				 'required' => false,
				 'name' => tr('Period unit'),
				 'description' => tr('Time unit used with "Period quantity"'),
				 'since' => '6.5, <s>7.x</s> & 8.0',
				 'doctype' => 'filter',
				 'filter' => 'word',
				 'options' => array(
					 array('text' => '', 'value' => ''),
					 array('text' => tr('Hour'), 'value' => 'hour'),
					 array('text' => tr('Day'), 'value' => 'day'),
					 array('text' => tr('Week'), 'value' => 'week'),
					 array('text' => tr('Month'), 'value' => 'month'),
				 ),
				 'default' => '',
			 ),
			 'periodType' => array(
				 'required' => false,
				 'name' => tr('Period type'),
				 'description' => tr('Time period after creation or after modification'),
				 'since' => '6.7, <s>7.x</s>, 8.4 & 9.0',
				 'doctype' => 'filter',
				 'filter' => 'word',
				 'options' => array(
					 array('text' => '', 'value' => ''),
					 array('text' => tr('Creation'), 'value' => 'c'),
					 array('text' => tr('Modification'), 'value' => 'm'),
				 ),
				 'default' => '',
			 ),
			 'editable' => array(
				 'required' => false,
				 'name' => tr('Inline edit'),
				 'description' => tr('Colon-separated list of fields for which inline editing will be enabled.'),
				 'since' => '11.0',
				 'filter' => 'digits',
				 'separator' => ':',
				 'profile_reference' => 'tracker_field',
				 'default' => '',
			 ),
			 'editableall' => array(
				 'required' => false,
				 'name' => tr('Inline edit All'),
				 'description' => tr('Allow all displayed fields to be editable'),
				 'since' => '11.0',
				 'default' => 'y',
				 'advanced' => true,
				 'filter' => 'alpha',
				 'options' => array(
					 array('text' => '', 'value' => ''),
					 array('text' => tra('Yes'), 'value' => 'y'),
					 array('text' => tra('No'), 'value' => 'n')
				 )
			 ),
			 'force_separate_compile' => array(
				'required' => false,
				'name' => tra('Compile Each Item'),
				'description' => tra('Compile each item separately instead of compiling the entire template.'),
				 'since' => '11.0',
				'filter' => 'alpha',
				'default' => 'y',
				'options' => array(
					array('text' => '', 'value' => ''),
					array('text' => tra('Yes'), 'value' => 'y'),
					array('text' => tra('No'), 'value' => 'n')
				)
			 ),
		), $tsparams
	);
	return array(
		'name' => tra('Tracker List'),
		'documentation' => 'PluginTrackerList',
		'description' => tra('List, filter and sort the items in a tracker'),
		'prefs' => array( 'feature_trackers', 'wikiplugin_trackerlist' ),
		'tags' => array( 'basic' ),
		'body' => tra('Notice'),
		'additional' => '<br>' . tr('Additional information when using tablesorter and the %0 parameter (Server Side Processing) is set to y:', '<code>server</code>') . '<ul>'
			. '<li>' . tra('Filtering and sorting on some field types (e.g., items list), may behave unexpectedly') . '</li>'
			. '<li>' . tra('For best results the date filter should only be applied to date field types') . '</li>'
			. '<li>' . tra('To filter the category field type, the exact category name or id needs to be entered') . '</li>'
			. '</ul>',
		'format' => 'html',
		'iconname' => 'table',
		'introduced' => 1,
		'filter' => 'text',
		'params' => $params
	);
}

function wikiplugin_trackerlist($data, $params)
{
	global $tiki_p_admin_trackers, $prefs, $tiki_p_view_trackers, $user,
		   $page, $tiki_p_tracker_vote_ratings, $tiki_p_tracker_view_ratings,
		   $tiki_p_export_tracker, $tiki_p_watch_trackers, $tiki_p_edit;

	$userlib = TikiLib::lib('user');
	$tikilib = TikiLib::lib('tiki');
	$trklib = TikiLib::lib('trk');
	$smarty = TikiLib::lib('smarty');
	$notificationlib = TikiLib::lib('notification');

	static $iTRACKERLIST = 0;
	++$iTRACKERLIST;
	$smarty->assign('iTRACKERLIST', $iTRACKERLIST);

	$default = array(
		'calendarfielddate' => '',
		'wiki' => '',
		'calendarviewmode' => 'month',
		'calendarstickypopup' => 'n',
		'calendarbeginmonth' => 'y',
		'calendarviewnavbar' => 'y',
		'calendartitle'=>'',
		'calendardelta' => '',
		'force_compile' => 'n',
		'editable' => array(),
		'editableall' => 'n',
	);

	$params = array_merge($default, $params);

	extract($params, EXTR_SKIP);

	$skip_status_perm_check = false;

	if(isset($force_separate_compile) && $force_separate_compile == 'y') {
		$smarty->assign('force_separate_compile', 'y');
	}

	if ($prefs['feature_trackers'] != 'y' || !isset($trackerId) || !($tracker_info = $trklib->get_tracker($trackerId))) {
		return $smarty->fetch("wiki-plugins/error_tracker.tpl");
	} else {

		global $auto_query_args;
		$auto_query_args_local = array('trackerId', 'tr_initial',"tr_sort_mode$iTRACKERLIST",'tr_user', 'filterfield', 'filtervalue', 'exactvalue', 'itemId', "tr_offset$iTRACKERLIST");
		$auto_query_args = empty($auto_query_args)? $auto_query_args_local: array_merge($auto_query_args, $auto_query_args_local);
		$smarty->assign('listTrackerId', $trackerId);
		$definition = Tracker_Definition::get($trackerId);
		$tracker_info = $definition->getInformation();

		if (!isset($sort)) {
			$sort = 'n';
		}

		$perms = $tikilib->get_perm_object($trackerId, 'tracker', $tracker_info, false);
		if ($perms['tiki_p_view_trackers'] != 'y' && !$user) {
			return;
		}
		$userCreatorFieldId = $definition->getAuthorField();
		$groupCreatorFieldId = $definition->getWriterGroupField();
		if ($perms['tiki_p_view_trackers'] != 'y' && ! $definition->isEnabled('writerCanModify') && ! $definition->isEnabled('userCanSeeOwn') && empty($userCreatorFieldId) && empty($groupCreatorFieldId)) {
			return;
		}
		$smarty->assign_by_ref('perms', $perms);

		if (!empty($fields)) {
			$limit = $fields;
		} else {
			$limit = '';
		}
		// Make sure limit is an array
		if (!is_array($limit) && !empty($limit)) {
			$limit = explode(':', $limit);
		}

		if (!empty($filterfield) && !empty($limit)) {
			$limit = array_unique(array_merge($limit, $filterfield));
		}
		
		// for some reason if param popup is set but empty, the array contains 2 empty elements. We filter them out.
		if (isset($popup)) {
			$popup = array_filter($popup);
			if (!empty($popup)) {
				$limit = array_unique(array_merge($limit, $popup));
			}
		}
		if (!empty($calendarfielddate)) {
			$limit = array_unique(array_merge($limit, $calendarfielddate));
		}
		if (!empty($limit) && $trklib->test_field_type($limit, array('C'))) {
			$limit = array();
		}

		$allfields = $trklib->list_tracker_fields($trackerId, 0, -1, 'position_asc', '', true, '', $trklib->flaten($limit));
		if (!empty($fields)) {
			$listfields = $fields;

			//We must include the $calendarfielddate, even if they are not in the listfields
			if (!empty($calendarfielddate)) {
				foreach ($calendarfielddate as $f) {
					if (!in_array($f, $listfields)) {
						$listfields[] = $f;
					}
				}
			}
			if ($sort == 'y') {
				$allfields = $trklib->sort_fields($allfields, $listfields);
			}
		} elseif (!empty($wiki) || !empty($tpl) || !empty($tplwiki)) {
				if (!empty($wiki)) {
					$listfields = $trklib->get_pretty_fieldIds($wiki, 'wiki', $prettyModifier, $trackerId);
				} elseif (!empty($tplwiki)) {
					$listfields = $trklib->get_pretty_fieldIds($tplwiki, 'wiki', $prettyModifier, $trackerId);
				} else {
					$listfields = $trklib->get_pretty_fieldIds($tpl, 'tpl', $prettyModifier, $trackerId);
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
		/*** tablesorter ***/
		//note whether ajax is needed
		$tsServer = isset($params['server']) && $params['server'] === 'y' ? true : false;

		$tsOn	= isset($sortable) && $sortable !== 'n' && Table_Check::isEnabled($tsServer);
		$smarty->assign('tsOn', $tsOn);

		//note whether this is the initial tablesorter ajax call or a subsequent ajax call
		$tsAjax = Table_Check::isAjaxCall();
		$smarty->assign('tsAjax', $tsAjax);

		if ($tsAjax) {
			// if status is enabled, need to adjust field index by -1 - need to check both - tracker config and plugin config
			$adjustCol = (isset($showstatus) && $showstatus == 'y' && $definition->isEnabled('showStatus')) ? -1 : 0;
			//convert tablesorter filter syntax to tiki syntax
			if (!empty($_REQUEST['filter'])) {
				$i = 0;
				$tsfiltersArray = explode('|', $tsfilters);
				foreach ($_REQUEST['filter'] as $col => $ajaxfilter) {
					$fieldtype = $allfields['data'][$col + $adjustCol]['type'];
					$id = $allfields['data'][$col + $adjustCol]['fieldId'];
					//handle status filter
					if ($adjustCol === -1 && $col === 0 && in_array($ajaxfilter, ['o','p','c'])) {
						$status = $ajaxfilter;
					/*
					 * handle date filter - these are always one filter, in the form of:
					 * from: >=1427389832000; to: <=1427389832000; both from and to: 1427389832000 - 1427880000000
					 * which is unix timestamp in milliseconds
					 */
					} elseif (strpos($tsfiltersArray[$col], 'type:date') !== false && in_array($fieldtype, ['f', 'j'])) {
						$datefilter = explode(' - ', $ajaxfilter);
						$filterfield[$i] = $id;
						//a range (from and to filters) will have 2 items in the array
						if (count($datefilter) == 2) {
							$filterfield[$i + 1] = $id;
							//use substr to leave off milliseconds since date is stored in seconds in the database
							$exactvalue[$i] = 'greaterequal(@' . substr($datefilter[0], 0, 10) . ')';
							$exactvalue[$i + 1] = 'lessequal(@' . substr($datefilter[1], 0, 10) . ')';
						} else {
							//use substr to leave off milliseconds since date is stored in seconds in the database
							$stamp = '(@' . substr($datefilter[0], 2, 10) . ')';
							$symbol = substr($datefilter[0], 0, 2);
							if ($symbol === '<=') {
								$compare = 'lessequal';
							} elseif ($symbol === '>=') {
								$compare = 'greaterequal';
							}
							$exactvalue[$i] = $compare . $stamp;
						}
					} else {
						$filterfield[$i] = $id;
						//convert category filters entered as text
						if ($fieldtype === 'e' && !is_numeric($ajaxfilter)) {
							$categlib = TikiLib::lib('categ');
							$ajaxfilter = $categlib->get_category_id($ajaxfilter);
						}
						$filtervalue[$i] = $ajaxfilter;
					}
					$i++;
				}
			}
			//convert tablesorter sort syntax to tiki syntax
			if (!empty($_REQUEST['sort'])) {
				foreach ($_REQUEST['sort'] as $sortcol => $ajaxsort) {
					if ($ajaxsort == '0') {
						$dir = '_asc';
					} elseif ($ajaxsort == '1') {
						$dir = '_desc';
					}
					//avoid setting sort_mode based on status field - will return error. Handle later once records are retrieved
					if ($adjustCol !== -1 || $sortcol !== 0) {
						$sort_mode = 'f_' . $allfields['data'][$sortcol + $adjustCol]['fieldId'] . $dir;
					}
				}
			}
			//set max records
			if (isset($_REQUEST['numrows'])) {
				$max = $_REQUEST['numrows'];
			}
		}
		/*** end first tablesorter section ***/

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

		$filter = array();

		if (isset($periodQuantity)) {
			switch ($periodUnit) {
				case 'hour':
					$periodUnit = 3600;
    				break;
				case 'day':
					$periodUnit = 86400;
    				break;
				case 'week':
					$periodUnit = 604800;
    				break;
				case 'month':
					$periodUnit = 2628000;
    				break;
				default:
    				break;
			}

			if (!isset($periodType)) {
				$periodType = 'c';
			}

			if (is_int($periodUnit) && ($periodType == 'm' ) ) {
				$filter['lastModifAfter'] = $tikilib->now - ($periodQuantity * $periodUnit);
				$filter['lastModifBefore'] = $tikilib->now;
			} elseif (is_int($periodUnit)) { # case for periodType beig c or anything else (therefore, set as case for default)
				$filter['createdAfter'] = $tikilib->now - ($periodQuantity * $periodUnit);
				$filter['createdBefore'] = $tikilib->now;
			}
		}

		if (isset($_REQUEST['reloff']) && empty($_REQUEST['itemId']) && !empty($_REQUEST['trackerId'])) { //coming from a pagination
			$items = $trklib->list_items(
				$_REQUEST['trackerId'],
				$_REQUEST['reloff'], 1, '', '',
				isset($_REQUEST['filterfield']) ? preg_split('/\s*:\s*/', $_REQUEST['filterfield']) : '',
				isset($_REQUEST['filtervalue']) ? preg_split('/\s*:\s*/', $_REQUEST['filtervalue']) : '',
				isset($_REQUEST['status']) ? preg_split('/\s*:\s*/', $_REQUEST['status']) : '',
				isset($_REQUEST['initial']) ? $_REQUEST['initial'] : '',
				isset($_REQUEST['exactvalue']) ? preg_split('/\s*:\s*/', $_REQUEST['exactvalue']) : '',
				$filter
			);
			if (isset($items['data'][0]['itemId'])) {
				$_REQUEST['cant'] = $items['cant'];
				$_REQUEST['itemId'] = $items['data'][0]['itemId'];
			}
		}

		if (!empty($_REQUEST['itemId']) && $tiki_p_tracker_vote_ratings == 'y' && $user) {
			$hasVoted = false;
			foreach ($allfields['data'] as $f) {
				if ($f['type'] == 's' && $definition->isEnabled('useRatings') && ($f['name'] == 'Rating' || $f['name'] = tra('Rating'))) {
					$i = $f['fieldId'];
					if (isset($_REQUEST["ins_$i"]) && ($_REQUEST["ins_$i"] == 'NULL' || in_array($_REQUEST["ins_$i"], explode(',', $tracker_info['ratingOptions'])))) {
						$trklib->replace_rating($trackerId, $_REQUEST['itemId'], $i, $user, $_REQUEST["ins_$i"]);
						$hasVoted = true;
					}
				} elseif ($f['type'] == '*' || $f['type'] == 'STARS') {
					$i = $f['fieldId'];
					if (isset($_REQUEST["ins_$i"])) {
						$trklib->replace_star($_REQUEST["ins_$i"], $trackerId, $_REQUEST['itemId'], $f, $user);
						$hasVoted = true;
					}
				}
			}
			if ($hasVoted) {
				// Must strip NULL for remove my vote case
				$url = preg_replace('/[(\?)|&]vote=y/', '$1', preg_replace('/[(\?)|&]ins_[0-9]+=-?[0-9|N|U|L]*/', '$1', $_SERVER['REQUEST_URI']));
				// reduce duplicate itemIds in query string
				$occurences = preg_match_all('/[(\?)|&]itemId=[0-9]+/', $url, $matches);
				if ($params['list_mode'] == 'y' && $occurences > 0) {
					$url = preg_replace('/[(\?)|&]itemId=[0-9]+/', '$1', $url, $occurences);
				} elseif ($occurences > 1) {
					$url = preg_replace('/&itemId=[0-9]+/', '', $url, $occurences - 1);
				}
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
			foreach ($allfields['data'] as $f) {
				$listfields[] = $f['fieldId'];
			}
		}
		if (!empty($popup)) {
			$popupfields = $popup;
		} else {
			$popupfields = array();
		}
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
		if (!isset($showpenditem)) {
			$showpenditem = 'n';
		}
		$smarty->assign_by_ref('showpenditem', $showpenditem);
		if (!isset($showcloseitem)) {
			$showcloseitem = 'n';
		}
		$smarty->assign_by_ref('showcloseitem', $showcloseitem);
		if (!isset($showopenitem)) {
			$showopenitem = 'n';
		}
		$smarty->assign_by_ref('showopenitem', $showopenitem);
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
		
		if (!isset($showlastmodifby)) {
			$showlastmodifby = $tracker_info['showLastModifBy'];
		}
		$smarty->assign_by_ref('showlastmodifby', $showlastmodifby);
		
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

		if (!isset($max)) {
			$max = $prefs['maxRecords'];
		}

		if (isset($_REQUEST["tr_offset$iTRACKERLIST"]) && (!isset($forceoffset) || $forceoffset == 'n')) {
			$tr_offset = $_REQUEST["tr_offset$iTRACKERLIST"];
		} else if (isset($offset) && $offset >= 0) {
			$tr_offset = $offset;
		} else {
			$tr_offset = 0;
		}
		$smarty->assign_by_ref("tr_offset$iTRACKERLIST", $tr_offset);

		$tr_initial = '';
		if ($showinitials == 'y') {
			if (isset($_REQUEST['tr_initial'])) {
			  //$query_array['tr_initial'] = $_REQUEST['tr_initial'];
				$tr_initial = $_REQUEST['tr_initial'];
			}
			$smarty->assign('initials', explode(' ', 'a b c d e f g h i j k l m n o p q r s t u v w x y z'));
		}
		$smarty->assign_by_ref('tr_initial', $tr_initial);

		if ((isset($view) && $view == 'user') || isset($view_user) || isset($_REQUEST['tr_user'])) {
			if ($f = $definition->getAuthorField()) {
				$filterfield[] = $f;
				$filtervalue[] = '';
				if (!isset($_REQUEST['tr_user'])) {
					$exactvalue[] = isset($view)? (empty($user)?'Anonymous':$user): $view_user;
				} else {
					$exactvalue[] = $_REQUEST['tr_user'];
					$smarty->assign_by_ref('tr_user', $exactvalue);
				}
				if ($definition->isEnabled('writerCanModify') or $definition->isEnabled('userCanSeeOwn')) {
					$skip_status_perm_check = true;
				}
			}
		}
		if (isset($view) && $view == 'page' && isset($_REQUEST['page'])) {
			if (($f = $trklib->get_page_field($trackerId))) {
				$filterfield[] = $f['fieldId'];
				$filtervalue[] = '';
				$exactvalue[] = $_REQUEST['page'];
			}
		}

		if (isset($view) && $view == 'ip') {
			if ($f = $definition->getAuthorIpField()) {
				$filterfield[] = $f;
				$filtervalue[] = '';
				$ip = $tikilib->get_ip_address();
				$exactvalue[] = $ip;
			}
		}

		if (!isset($filtervalue)) {
			$filtervalue = '';
		} else {
			foreach ($filtervalue as $i=>$f) {
				if ($f == '#user') {
					$filtervalue[$i] = $user;
				} else if ($f == '#default_group') {
					$filtervalue[$i] = $_SESSION['u_info']['group'];
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
			if (is_string($itemId) && strstr($itemId, ':')) {	// JB Tiki7: This doesn't quite make sense as itemId is an array
				$itemId = explode(':', $itemId);				//			 Probably just some redundant code TOKIL
			}
			$filter['tti.`itemId`'] = $itemId;
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
								$categlib = TikiLib::lib('categ');
								if (ctype_digit($matches[2]) && $matches[2] > 0) {
									$cfilter = array('identifier'=>$matches[2], 'type'=>'descendants');
								} else {
									$cfilter = NULL;
								}
								$categs = $categlib->getCategories($cfilter, true, false);
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
										$utid = $userlib->get_tracker_usergroup($user);
										$matches[4] = $trklib->get_item_id($utid['usersTrackerId'], $utid['usersFieldId'], $user);
									}
								}
								if (!empty($matches[4])) {
									$l = $trklib->get_item_value(0, $matches[4], $matches[2]);
									$field = $trklib->get_tracker_field($matches[2]);
									if ($field['type'] == 'r') {
										$refItemId = $l;
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
		if ($tiki_p_admin_trackers != 'y' && $perms['tiki_p_view_trackers'] != 'y' && ($definition->isEnabled('writerCanModify') or $definition->isEnabled('userCanSeeOwn')) && $user && $userCreatorFieldId) { //patch this should be in list_items
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
			// If listfields is a colon separated string, convert it to an array
			if (!is_array($listfields)) {
				$listfields = explode(':', $listfields);
			}
			if ((in_array($allfields["data"][$i]['fieldId'], $listfields) or in_array($allfields["data"][$i]['fieldId'], $popupfields))and $allfields["data"][$i]['isPublic'] == 'y') {
				$passfields["{$allfields["data"][$i]['fieldId']}"] = $allfields["data"][$i];
			}
			if (isset($check['fieldId']) && $allfields["data"][$i]['fieldId'] == $check['fieldId']) {
				$passfields["{$allfields["data"][$i]['fieldId']}"] = $allfields["data"][$i];
				if (!in_array($allfields["data"][$i]['fieldId'], $listfields))
					$allfields["data"][$i]['isPublic'] == 'n'; //don't show it
				$check['ix'] = count($passfields) -1;
			}
			if ($allfields["data"][$i]['name'] == 'page' && empty($filterfield) && empty($displayList) && !empty($view) && $view == 'page') {
				$filterfield = $allfields["data"][$i]['fieldId'];
				$filtervalue = $_REQUEST['page'];
			}
			if ($definition->isEnabled('useRatings')
					and $allfields["data"][$i]['type'] == 's' and $allfields["data"][$i]['name'] == 'Rating') {
				$newItemRateField = $allfields["data"][$i]['fieldId'];
			}
		}
		$nonPublicFieldsWarning = '';
		if ($tiki_p_edit === 'y') {
			foreach ($allfields['data'] as $field) {
				if ($field['isPublic'] !== 'y' && in_array($field['fieldId'], array_merge($listfields, $popupfields))) {
					$nonPublicFieldsWarning = tra('You have attempted to view data of a tracker field which is not public. You need to ask the admin to change the setting to public for this field.');
				}
			}
		}
		if ($editableall=='y') {
			$editable = $listfields;
		}
		$smarty->assign('nonPublicFieldsWarning', $nonPublicFieldsWarning);
		$smarty->assign_by_ref('filterfield', $filterfield);
		$smarty->assign_by_ref('filtervalue', $filtervalue);
		$smarty->assign_by_ref('fields', $passfields);
		$smarty->assign_by_ref('exactvalue', $exactvalue);
		$smarty->assign_by_ref('listfields', $listfields);
		$smarty->assign_by_ref('popupfields', $popupfields);
		$smarty->assign('editableFields', $editable);
		if (!empty($filterfield)) {
			$urlquery['filterfield'] =  is_array($filtervalue) ? implode(':', $filterfield) : $filterfield;
			if (!is_array($filtervalue)) {
				$filtervalue = array($filtervalue);
			}
			$urlquery['filtervalue'] = is_array($filtervalue) ? implode(':', $filtervalue) : $filtervalue;
			$urlquery['exactvalue'] = is_array($exactvalue) ? implode(':', $exactvalue) : $exactvalue;
			$urlquery['trackerId'] = $trackerId;
			$smarty->assign('urlquery', $urlquery);
		} else {
			$smarty->assign('urlquery', '');
		}
		if (!empty($export) && $export != 'n' && $perms['tiki_p_export_tracker'] == 'y') {
			$smarty->loadPlugin('smarty_function_service');
			$exportParams = array(
				'controller' => 'tracker',
				'action' => 'export',
				'trackerId' => $trackerId,
			);
			if (!empty($fields)) {
				$exportParams['displayedFields'] = is_array($fields)? implode(':', $fields) : $fields;
			}
			if (is_array($filterfield)) {
				foreach ($filterfield as $i=>$fieldId) {
					$exportParams["f_$fieldId"] = empty($filtervalue[$i]) ? $exactvalue[$i] : $filtervalue[$i];
				}
			} elseif (!empty($filterfield)) {
				$exportParams["f_$filterfield"] = empty($filtervalue) ? $exactvalue : $filtervalue;
			}
			$exportUrl = smarty_function_service($exportParams, $smarty);
			$smarty->assign('exportUrl', $exportUrl);
		}

		if (!empty($_REQUEST['delete'])) {
			$itemToDelete = Tracker_Item::fromId($_REQUEST['delete']);
			if ($itemToDelete->canRemove()) {
				$trklib->remove_tracker_item($_REQUEST['delete']);
			}

			if (!empty($urlafterdelete)) {
				header("Location: $urlafterdelete");
				exit;
			}

		}
		if (!empty($_REQUEST['closeitem'])) {
			$itemToModify = Tracker_Item::fromId($_REQUEST['closeitem']);
			if ($itemToModify->canModify()) {
				$trklib->change_status(array(array('itemId' => $_REQUEST['closeitem'])), 'c');
			}
		}
		if (!empty($_REQUEST['penditem'])) {
			$itemToModify = Tracker_Item::fromId($_REQUEST['penditem']);
			if ($itemToModify->canModify()) {
				$trklib->change_status(array(array('itemId' => $_REQUEST['penditem'])), 'p');
			}
		}
		if (!empty($_REQUEST['openitem'])) {
			$itemToModify = Tracker_Item::fromId($_REQUEST['openitem']);
			if ($itemToModify->canModify()) {
				$trklib->change_status(array(array('itemId' => $_REQUEST['openitem'])), 'o');
			}
		}
		if (!empty($calendarfielddate)) {
			$calendarlib = TikiLib::lib('calendar');
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

			if (is_array($filterfield) == false) {
				$filterfield = array($filterfield);
			}

			if (is_array($$filtervalue) == false) {
				$filtervalue = array($filtervalue);
			}

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
					if (array_key_exists('not', array($exactvalue[$k]))) {
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
			//fetch tracker items
			$items = $trklib->list_items(
				$trackerId,
				$tr_offset,
				$max,
				$tr_sort_mode,
				$passfields,
				(!empty($calendarfielddate) ? null : $filterfield),
				$filtervalue,
				$tr_status,
				$tr_initial,
				$exactvalue,
				$filter,
				$allfields,
				$skip_status_perm_check
			);
			/*** tablesorter ***/
			if($tsOn && ! $tsAjax) {
				// when using serverside filtering check wether a dropdown is in use
				// and we must take params from tracker definition because no explicit options have been defined
				if ($tsServer) {
					//format from plugin: type:text|type:dropdown;option:1=Open;option:2=Closed|type:text|type:nofilter|type:nofilter|type:nofilter
					if (!empty($tsfilters) && strpos($tsfilters, 'dropdown') !== false) {
						$tsfiltersArray = explode('|', $tsfilters);
						$adjustCol = (isset($showstatus) && $showstatus == 'y' && $definition->isEnabled('showStatus')) ? -1 : 0;
						foreach ($tsfiltersArray as $col => &$tsfilterField) {
							// only consider dropdown definitions without explicit option
							if (strpos($tsfilterField, 'dropdown') !== false && strpos($tsfilterField, 'option') === false ) {
								//content from options (json object): {"options":["1=Open"," 2=Closed]} - note there can be whitespaces - it should not but there can be - yet another fix required
								if ( $allfields['data'][$col + $adjustCol]['type'] == 'd') { 
									$options =  $allfields['data'][$col + $adjustCol]['options'];
									$options = json_decode($options);
									$options = $options->options;
									// construct the new dropdown filterfield entry from the trackerfield definition
									$newTsfilterField = 'type:dropdown';
									foreach ($options as $option) {
										$newTsfilterField .= ";option:". trim($option);
									}
									// update field - note that we used a ref
									$tsfilterField = $newTsfilterField;
								}
							}
						}
						// update tsfilters
						$tsfilters = implode('|', $tsfiltersArray);
					}
				}
				$ts_id = 'wptrackerlist' . $trackerId . '-' . $iTRACKERLIST;
				$ts = new Table_Plugin;
				$ts->setSettings(
					$ts_id,
					isset($server) ? $server : null,
					$sortable,
					isset($sortList) ? $sortList : null,
					isset($tsortcolumns) ? $tsortcolumns : null,
					isset($tsfilters) ? $tsfilters : null,
					isset($tsfilteroptions) ? $tsfilteroptions : null,
					isset($tspaginate) ? $tspaginate : null,
					isset($tscolselect) ? $tscolselect : null,
					$GLOBALS['requestUri'],
					$items['cant'],
					isset($tstotals) ? $tstotals : null,
					isset($tstotaloptions) ? $tstotaloptions : null
				);
				//loads the jquery tablesorter code
				if (is_array($ts->settings)) {
					$ts->settings['ajax']['offset'] = 'tr_offset' . $iTRACKERLIST;
					Table_Factory::build('PluginWithAjax', $ts->settings);
				}
			}
			//determine whether totals will be added to bottom of table
			if (isset($ts->settings)) {
				Table_Totals::setTotals($ts->settings);
			}
			//handle certain tablesorter sorts
			if (isset($sortcol) && $items['cant'] > 1) {
				$fieldtype = $items['data'][0]['field_values'][$sortcol + $adjustCol]['type'];
				//convert categoryId sort to category name sort when tablesorter server side sorting is used
				if ($fieldtype === 'e') {
					foreach ($items['data'] as $key => $record) {
						$catfield = $record['field_values'][$sortcol + $adjustCol];
						$sortarray[$key] = $catfield['list'][$catfield['value']]['name'];
					}
				//sort status
				} elseif ($adjustCol === -1 && $sortcol === 0) {
					$sortarray = array_column($items['data'], 'status');
				}
				array_multisort($sortarray, $dir == '_desc' ? SORT_DESC : SORT_ASC, $items['data']);
			}
			/*** end second tablesorter section ***/

			if (isset($silent) && $silent == 'y' && empty($items['cant'])) {
				return;
			}

			if (isset($items['cant']) && $items['cant'] == 1 && isset($goIfOne) && ($goIfOne == 'y' || $goIfOne == 1)) {
				header('Location: tiki-view_tracker_item.php?itemId='.$items['data'][0]['itemId'].'&amp;trackerId='.$items['data'][0]['trackerId']);
				die;
			}

			if ($newItemRateField && !empty($items['data'])) {
				foreach ($items['data'] as $f=>$v) {
					$items['data'][$f]['my_rate'] = $tikilib->get_user_vote("tracker.".$trackerId.'.'.$items['data'][$f]['itemId'], $user);
				}
			}
			
			if (!empty($items['data']) && ($definition->isEnabled('useComments') && $definition->isEnabled('showComments') || $definition->isEnabled('showLastComment') )) {
				foreach ($items['data'] as $itkey=>$oneitem) {
					if ($definition->isEnabled('showComments')) {
						$items['data'][$itkey]['comments'] = $trklib->get_item_nb_comments($items['data'][$itkey]['itemId']);
					}
					if ($definition->isEnabled('showLastComment')) {
						$l = $trklib->list_last_comments($items['data'][$itkey]['trackerId'], $items['data'][$itkey]['itemId'], 0, 1);
						$items['data'][$itkey]['lastComment'] = !empty($l['cant']) ? $l['data'][0] : '';
					}
				}
			}
			
			if (!empty($items['data']) && ($definition->isEnabled('useAttachments') && $definition->isEnabled('showAttachments'))) {
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
					$value = array_sum($amount);
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
				$smarty->assign('today', TikiLib::make_time(0, 0, 0, TikiLib::date_format('%m'), TikiLib::date_format('%d'), TikiLib::date_format('%Y')));
				$smarty->assign('sticky_popup', $calendarstickypopup);
				$smarty->assign('calendar_popup', $calendarpopup);
				$smarty->assign('showpopup', 'n');
				$headerlib = TikiLib::lib('header');
				$headerlib->add_cssfile('themes/base_files/feature_css/calendar.css', 20);
				return $smarty->fetch('modules/mod-calendar_new.tpl');
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
				$smarty->assign("tr_offset$iTRACKERLIST", $_REQUEST['reloff']);
			} else {
				$smarty->assign_by_ref('max', $max);
				$smarty->assign_by_ref('item_count', $items['cant']);
				$smarty->assign_by_ref('count_item', $items['cant']);
				$smarty->assign('offset_arg', "tr_offset$iTRACKERLIST");
			}
			$smarty->assign_by_ref('items', $items["data"]);
			$smarty->assign('daformat', $tikilib->get_long_date_format()." ".tra("at")." %H:%M");

			if (!empty($params['showmap']) && $params['showmap'] == 'y') {
				$smarty->assign('trackerlistmapview', true);
				$smarty->assign('trackerlistmapname', "trackerlistmap_$iTRACKERLIST");
			} else {
				$smarty->assign('trackerlistmapview', false);
			}

			if (isset($items['data'])) {
				foreach ($items['data'] as $score_item) {
					$item_info = $trklib->get_tracker_item($score_item['itemId']);
					$currentItemId = $score_item['itemId'];

					TikiLib::events()->trigger('tiki.trackeritem.view',
						array(
							'type' => 'trackeritem',
							'object' => $currentItemId,
							'owner' => $item_info['createdBy'],
							'user' => $GLOBALS['user'],
						)
					);
				}
			}

			$tracker = $trklib->get_tracker($trackerId, 0, -1);
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
				return $smarty->fetch("error_raw.tpl");
			} else {
				$save_fc = null;
				if (!empty($wiki) && $params['force_compile'] === 'y') { // some pretty trackers need to compile fresh for each item
					$save_fc = $smarty->force_compile;
					$smarty->force_compile = true;
				}


				//this options preloads the javascript for displaying sheets
				if (!empty($displaysheet) && $displaysheet == 'y') {
					$headerlib = TikiLib::lib("header");
					$sheetlib = TikiLib::lib("sheet");

					$sheetlib->setup_jquery_sheet();
					$headerlib->add_jq_onready(
						'$("div.trackercontainer").sheet($.extend($.sheet.tikiOptions,{
							editable:false,
							buildSheet: true,
							minSize: {rows: 0, cols: 0}
						}));'
					);

					$smarty->assign('displaysheet', 'true');
				}

				//this method sets up the sheet just like it would for jquery.sheet, but assumes that the javascript will be handled elsewere
				if (!empty($tableassheet) && $tableassheet == 'y') {
					$smarty->assign('tableassheet', 'true');
				}
				$smarty->assign('context', $params);
				try {
					$str = $smarty->fetch('wiki-plugins/wikiplugin_trackerlist.tpl');
				} catch (SmartyException $e) {
					$str = $e->getMessage();
				}
				if ($save_fc !== null) {
					$smarty->force_compile = $save_fc;	// presumably will be false but put it back anyway
				}

				return $str;
			}
		} else {
			$smarty->assign('msg', tra("No field indicated"));
			return $smarty->fetch("error_raw.tpl");
		}
	}
	return $back;
}
