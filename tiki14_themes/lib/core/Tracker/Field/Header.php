<?php
// (c) Copyright 2002-2014 by authors of the Tiki Wiki CMS Groupware Project
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
				'description' => tr('Displays a header between fields to delimit a section and allow folding the fields.'),
				'readonly' => true,
				'help' => 'Header Tracker Field',
				'prefs' => array('trackerfield_header'),
				'tags' => array('basic'),
				'default' => 'y',
				'params' => array(
					'level' => array(
						'name' => tr('Header Level'),
						'description' => tr('Level of the header to use for complex tracker structures needing multiple heading levels.'),
						'default' => 1,
						'filter' => 'int',
						'legacy_index' => 0,
					),
					'toggle' => array(
						'name' => tr('Default State'),
						'description' => tr('Controls the section toggles'),
						'filter' => 'alpha',
						'default' => 'o',
						'options' => array(
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
		$level = intval($this->getOption('level', 2));
		if ($level <= 0) {
			$level = 2;
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
	var processTableForHeaders = function( $table ) {
		var $hdr, $descdiv, $newtable = $("<table>").attr("class", $table.attr("class"));
		$("tr", $table).each(function() {	// step through each row
			if ($(".hdrField", this).length) {	// chop the table...
				var $this = $(this);
				var $sibs = $this.nextAll("tr");
				var level = $(".hdrField:first", this).data("level");
				var name = $(".hdrField:first", this).data("name");
				$hdr = $("<h" + level + ">").text($.trim(name));
				$descdiv = $("div.' . $desclass . '", this);
				var toggle = $(".hdrField:first", this).data("toggle");
				if (toggle) {
					$hdr.click(function(){
						$newtable.toggle();
						if (typeof $descdiv != \'undefined\') {
							$descdiv.toggle();
						}
						$(this).toggleClass("trackerHeaderClose")
								.toggleClass("trackerHeaderOpen");
					}).addClass(toggle === "c" ? "trackerHeaderClose" : "trackerHeaderOpen");
					if (toggle === "c") {
						$newtable.hide();
						if (typeof $descdiv != \'undefined\') {
							$descdiv.hide();
						}
					}
				}
				$sibs.each(function(){
					$newtable.append(this);
					$this.remove();
				});
				return false;
			}
		});
		$table.after($newtable).after($hdr);
		if (typeof $descdiv != \'undefined\' && $("tr", $newtable).length > 0){
			$newtable.before($descdiv);
		}
		if ($("tr", $newtable).length) {
			processTableForHeaders($newtable);	// recurse until done
		}
	}
	$(".hdrField").parents("table").each(function() {
		processTableForHeaders($(this));
	});
})();';
		} else {
			$js = '';	// TODO div mode for plugins or something
		}
		$headerlib->add_jq_onready($js);
		
		// just a marker for jQ to find
		$html = '<span class="hdrField' . $class . '" data-level="' . $level . '" ' . '" data-name="' . $name . '" '
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

