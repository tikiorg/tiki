/* ***** BEGIN LICENSE BLOCK *****
 * Version: MPL 1.1/GPL 2.0/LGPL 2.1
 *
 * The contents of this file are subject to the Mozilla Public License Version
 * 1.1 (the "License"); you may not use this file except in compliance with
 * the License. You may obtain a copy of the License at
 * http://www.mozilla.org/MPL/
 *
 * Software distributed under the License is distributed on an "AS IS" basis,
 * WITHOUT WARRANTY OF ANY KIND, either express or implied. See the License
 * for the specific language governing rights and limitations under the
 * License.
 *
 * The Original Code is SUMO tools
 *
 * The Initial Developer of the Original Code is
 * Mozilla Corporation.
 * Portions created by the Initial Developer are Copyright (C) 2009
 * the Initial Developer. All Rights Reserved.
 *
 * Contributor(s):
 *  Paul Craciunoiu <pcraciunoiu@mozilla.com>
 *
 * Alternatively, the contents of this file may be used under the terms of
 * either the GNU General Public License Version 2 or later (the "GPL"), or
 * the GNU Lesser General Public License Version 2.1 or later (the "LGPL"),
 * in which case the provisions of the GPL or the LGPL are applicable instead
 * of those above. If you wish to allow use of your version of this file only
 * under the terms of either the GPL or the LGPL, and not to allow others to
 * use your version of this file under the terms of the MPL, indicate your
 * decision by deleting the provisions above and replace them with the notice
 * and other provisions required by the GPL or the LGPL. If you do not delete
 * the provisions above, a recipient may use your version of this file under
 * the terms of any one of the MPL, the GPL or the LGPL.
 *
 * ***** END LICENSE BLOCK ***** */
$(document).ready(function () {
	// initiate tabs, creating separate 'add new' tab
	$('.jqtabs').tabs({
		select: function (event, ui) {
			var url = $.data(ui.tab, 'load.tabs').toString();
			if (url.indexOf('tiki-admin_metrics.php') !== -1) {
				location.href = $.data(ui.tab, 'load.tabs') + '#editcreatetab';
				return false;
			}
		}
	});
	// force redraw on every tabshow, required for proper display of sparklines
	$('.jqtabs').bind('tabsshow', function (event, ui) {
		$('.inlinesparkline').sparkline('html', {width:'200px', height:'30px'});
		$('.toggle-button').html('');
		$('.metricbox').children('.toggle').hide();
		$('.toggle-button').click(function () {
			$(this).attr('class', 'toggle-button-off');
			$(this).parents('.metricbox').children('.toggle').toggle();
			if ($(this).parents('.metricbox').children('.toggle:visible').size()) {
				$(this).attr('class', 'toggle-button toggle-button-off');
			}
			else {
				$(this).attr('class', 'toggle-button');
			}
		});
	});

	// show and hide the labels for date range fields as the option changes
	$('#metrics-range-select').change(function () {
		var sel = $("select option:selected").attr('value');
		if (sel === 'lastweek') {
			$('#range-inputs').hide();
		} else
		if (sel === 'weekof') {
			$('#range-inputs').show();
			$('#range-inputs .range-custom-text').hide();
			$('#range-inputs .range-monthof-text').hide();
			$('#range-date-to').hide();
			$('#range-inputs .range-weekof-text').show();
		} else if (sel === 'monthof') {
			$('#range-inputs').show();
			$('#range-inputs .range-custom-text').hide();
			$('#range-inputs .range-weekof-text').hide();
			$('#range-date-to').hide();
			$('#range-inputs .range-monthof-text').show();
		} else {
			//custom
			$('#range-inputs').show();
			$('#range-inputs .range-weekof-text').hide();
			$('#range-inputs .range-monthof-text').hide();
			$('#range-date-to').show();
			$('#range-inputs .range-custom-text').show();
		}
	});
	$('#metrics-range-select').change();
});
