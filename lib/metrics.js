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
$jq(document).ready(function () {
	// initiate tabs, creating separate 'add new' tab
	$jq('.jqtabs').tabs({
		select: function (event, ui) {
			var url = $jq.data(ui.tab, 'load.tabs').toString();
			if (url.indexOf('tiki-admin_metrics.php') !== -1) {
				location.href = $jq.data(ui.tab, 'load.tabs') + '#editcreatetab';
				return false;
			}
		}
	});
	// force redraw on every tabshow, required for proper display of sparklines
	$jq('.jqtabs').bind('tabsshow', function (event, ui) {
		$jq('.inlinesparkline').sparkline('html', {width:'200px', height:'30px'});
		$jq('.toggle-button').html('');
		$jq('.metricbox').children('.toggle').hide();
		$jq('.toggle-button').click(function () {
			$jq(this).attr('class', 'toggle-button-off');
			$jq(this).parents('.metricbox').children('.toggle').toggle();
			if ($jq(this).parents('.metricbox').children('.toggle:visible').size()) {
				$jq(this).attr('class', 'toggle-button toggle-button-off');
			}
			else {
				$jq(this).attr('class', 'toggle-button');
			}
		});
	});

	// show and hide the labels for date range fields as the option changes
	$jq('#metrics-range-select').change(function () {
		var sel = $jq("select option:selected").attr('value');
		if (sel === 'lastweek') {
			$jq('#range-inputs').hide();
		} else
		if (sel === 'weekof') {
			$jq('#range-inputs').show();
			$jq('#range-inputs .range-custom-text').hide();
			$jq('#range-inputs .range-monthof-text').hide();
			$jq('#range-date-to').hide();
			$jq('#range-inputs .range-weekof-text').show();
		} else if (sel === 'monthof') {
			$jq('#range-inputs').show();
			$jq('#range-inputs .range-custom-text').hide();
			$jq('#range-inputs .range-weekof-text').hide();
			$jq('#range-date-to').hide();
			$jq('#range-inputs .range-monthof-text').show();
		} else {
			//custom
			$jq('#range-inputs').show();
			$jq('#range-inputs .range-weekof-text').hide();
			$jq('#range-inputs .range-monthof-text').hide();
			$jq('#range-date-to').show();
			$jq('#range-inputs .range-custom-text').show();
		}
	});
	$jq('#metrics-range-select').change();
});
