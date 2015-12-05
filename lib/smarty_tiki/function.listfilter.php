<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

// this script may only be included - so it's better to die if called directly
if (strpos($_SERVER["SCRIPT_NAME"], basename(__FILE__)) !== false) {
  header("location: index.php");
  die;
}

/**
 * \brief JQuery Smarty function to filter list of results (by default table)
 *
 * Params
 *
 * @id				id of the input field
 * @size			size of the input field
 * @maxlength		max length of the input field in characters
 * @prefix			prefix text to be put before the input field
 * @selectors		CSS (jQuery) selector(s) for what to filter
 * @exclude			selector(s) for what to exclude from the text filter
 * 						(but still hide when parent is empty)
 * @query			key/field name for presetting filter box value from the URL
 * 						e.g. tiki-admin.php?page=textarea&filter=blog
 * 						(default="textFilter" - set to an empty string to disable)
 *
 * Mainly for treetable lists...
 * @parentSelector	CSS (jQuery) selector(s) for parent nodes of what to filter
 * @childPrefix = 'child-of-'	prefix for child class (to hide parent if all children are hidden by the filter)
 *
 * @return html string (with jQuery added to headerlib)
 */



function smarty_function_listfilter($params, $smarty)
{
	global $prefs, $listfilter_id;
	$headerlib = TikiLib::lib('header');

	if ($prefs['feature_jquery'] != 'y' || $prefs['javascript_enabled'] != 'y') {
		return '';
	} else {
		extract($params);
		$childPrefix = isset($childPrefix) ? $childPrefix : 'child-of-';
		$exclude = isset($exclude) ? $exclude : '';

		$input = ' <div class="form-horizontal"><div class="form-group"><label class="col-sm-2 control-label">';

		if (!isset($prefix)) {
			$input .= tra("Filter:");
		} else {
			$input .= tra($prefix);
		}
		$input .= '</label><div class="col-sm-5"><input type="text" class="form-control listfilter"';
		if (!isset($id)) {
			if (isset($listfilter_id)) {
				$listfilter_id++;
			} else {
				$listfilter_id = 1;
			}
			$id = "listfilter_$listfilter_id";
			$input .= " id='$id'";
		} else {
			$input .= " id='$id'";
		}
		if (isset($size)) $input .= " size='$size'";
		if (isset($maxlength)) $input .= " maxlength='$maxlength'";

		// value from url
		if (!isset($query)) {
			$query = 'textFilter';
		}
		if (!empty($query) && !empty($_REQUEST[$query])) {
			$input .= ' value="' . $_REQUEST[$query] . '"';
		} elseif (!empty($editorId)) {
			$parentTabId = (empty($parentTabId) ? "" : $parentTabId);

			$headerlib->add_jq_onready(
				"
				$(document).bind('editHelpOpened', function() {
					var text = getTASelection('#".$editorId."'),
					possiblePlugin = text.split(/[ \(}]/)[0];
					if (possiblePlugin.charAt(0) == '{') { //we have a plugin here
						possiblePlugin = possiblePlugin.substring(1);
						$('#$id')
							.val(possiblePlugin)
							.trigger('keyup');

						var parentTabId = '".$parentTabId."';
						if (parentTabId) {
							$('#help_sections a[href=#$parentTabId]').trigger('click');
							var pluginTr = $('#plugins_help_table tr').not(':hidden');

							if (pluginTr.length == 1) {
								pluginTr.find('a:first').click();
							}
						}
					}
				});
			"
			);
		}

		$input .= "></div><div class='col-sm-5'>";
		$smartylib = TikiLib::lib('smarty');
		$smartylib->loadPlugin('smarty_function_icon');
		$icon = smarty_function_icon(['name' => 'delete'], $smarty);
		$input .= "<a href='#' onclick=\"\$('#$id').val('').focus().keyup();return false;\" class='closeicon tips' title=':"
				. tr('Clear fiilter') . "'>$icon</a>";
		$input .= '</div></div></div>';

		if (!isset($selectors)) $selectors = ".$id table tr";

		$content = "
$('#$id').keyup( function() {
	var criterias = this.value.toLowerCase().split( /\s+/ );
	$('$selectors').each( function() {
		var text = $(this).text().toLowerCase();
		for( i = 0; criterias.length > i; ++i ) {
			word = criterias[i];
			if ( word.length > 0 && text.indexOf( word ) == -1 ) {
				$(this).not('$exclude').hide();	// don't search within excluded elements
				return;
			}
		}
		$(this).show();
	} );
";
		if (!empty($parentSelector)) {
			$content .= "
	\$('$parentSelector').show().each( function() {
		if (\$('{$selectors}[data-tt-parent-id=' + \$(this).data('tt-id') + ']:visible:not(\"$exclude\")').length == 0) {	// excluded things don't count
			\$(this).hide();
			\$('{$exclude}[data-tt-parent-id=' + \$(this).data('tt-id') + ']').hide();							// but need hiding if the parent is 'empty'
		} else {
			\$(this).removeClass('collapsed').addClass('expanded');
		}
	});
";
		}
		$content .= '
} );	// end keyup
';
		if (!empty($query) && !empty($_REQUEST[$query])) {
			$content .= "
setTimeout(function () {
	if ($('#$id').val() != '') {
		$('#$id').keyup();
	}
}, 1000);
";
		}

		$headerlib->add_jq_onready($content);
		return $input;
	}
}
