<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

/**
 * Handler class for Header
 * 
 * Letter key: ~h~
 *
 */
class Tracker_Field_Header extends Tracker_Field_Abstract implements Tracker_Field_Synchronizable
{
	public static function getTypes()
	{
		return array(
			'h' => array(
				'name' => tr('Header'),
				'description' => tr('Displays a heading between fields to delimit a section and allow folding the fields.'),
				'readonly' => true,
				'help' => 'Header Tracker Field',
				'prefs' => array('trackerfield_header'),
				'tags' => array('basic'),
				'default' => 'y',
				'params' => array(
					'level' => array(
						'name' => tr('Heading Level'),
						'description' => tr('Level of the heading to use for complex tracker structures needing multiple heading levels.'),
						'default' => 3,
						'filter' => 'int',
						'legacy_index' => 0,
					),
					'toggle' => array(
						'name' => tr('Section Toggle'),
						'description' => tr('Default State'),
						'filter' => 'alpha',
						'default' => 'o',
						'options' => array(
							'' => tr('No toggle'),
							'o' => tr('Open'),
							'c' => tr('Closed'),
						),
						'legacy_index' => 1,
					),
				),
			),
		);
	}

	function getFieldData(array $requestData = array())
	{
		$ins_id = $this->getInsertId();

		return array();
	}

	function renderInput($context = array())
	{
		return $this->renderOutput($context);
	}
	
	function renderOutput($context = array())
	{
		if (isset($context['list_mode']) && $context['list_mode'] === 'csv') {
			return;
		}
		global $prefs;
		$headerlib = TikiLib::lib('header');

		$class = null;
		$level = intval($this->getOption('level', 3));
		if ($level <= 0) {
			$level = 3;
		}
		$toggle = $this->getOption('toggle');
		$inTable = isset($context['inTable']) ? $context['inTable'] : '';
		$name =  htmlspecialchars(tra($this->getConfiguration('name')));
		//to distinguish header description display on tiki-view_tracker.php versus when plugin tracker is used
		$desclass = isset($context['pluginTracker']) && $context['pluginTracker'] == 'y' ?
			'trackerplugindesc' : 'description';
		$data_toggle = '';
		if ($prefs['javascript_enabled'] === 'y' && ($toggle === 'o' || $toggle === 'c')) {
			$class = ' ' . ($toggle === 'c' ? 'trackerHeaderClose' : 'trackerHeaderOpen');
			$data_toggle = 'data-toggle="' . $toggle . '"';
		}
		if ($inTable) {
			$js = '
(function() {
	var processTrackerPageForHeaders = function( $div ) {
		if ($(".hdrField", $div).length) {	// check
			var $hdrField = $(".hdrField:first", $div);
			var level = $hdrField.data("level");
			var name = $hdrField.data("name");
			var toggle = $hdrField.data("toggle");

			$hdr = $("<h" + level + ">").text($.trim(name));

			if (toggle) {
				var $section = $div.nextUntil(":not(div)");
				$hdr.click(function(){
					$section.toggle();
					var $i = $("i", this);
					if ($i.hasClass("fa-chevron-right")) {
						$i.replaceWith("<i class=\"fa fa-chevron-down\"></i>");
					} else {
						$i.replaceWith("<i class=\"fa fa-chevron-right\"></i>");
					}
				});
				if (toggle === "c") {
					$hdr.append("<small> <i class=\"fa fa-chevron-right\"></i></small>");
					$section.hide();
				} else {
					$hdr.append("<small> <i class=\"fa fa-chevron-down\"></i></small>");
				}
			}
			$div.replaceWith($hdr);
			return false;
		}
	}
	$(".hdrField").parents(".form-group").each(function() {
		processTrackerPageForHeaders($(this));
	});
})();';
		} else {
			$js = '';	// TODO div mode for plugins or something
		}
		$headerlib->add_jq_onready($js);
		
		// just a marker for jQ to find
		$html = '<span class="hdrField' . $class . '" data-level="' . $level . '" ' . ' data-name="' . $name . '" '
			. $data_toggle .' style="display:none;"></span>';
		
		return $html;
	}

	function importRemote($value)
	{
		return '';
	}

	function exportRemote($value)
	{
		return '';
	}

	function importRemoteField(array $info, array $syncInfo)
	{
		return $info;
	}
}

