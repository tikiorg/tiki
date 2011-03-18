<?php

/**
 * Handler class for Header
 * 
 * Letter key: ~h~
 *
 */
class Tracker_Field_Header extends Tracker_Field_Abstract
{
	function getFieldData(array $requestData = array())
	{
		$ins_id = $this->getInsertId();

		return array();
	}

	function renderInput($context = array())
	{
		return $this->renderOutput( $context );
	}
	
	function renderOutput($context = array())
	{
		global $prefs;
		$headerlib = TikiLib::lib('header');

		$level = $this->getOption(0, 2);
		$toggle = $this->getOption(1);
		$inTable = isset($context['inTable']) ? $context['inTable'] : '';
		$id = 'hdrField_' . $this->getConfiguration('fieldId') . '_' . $this->getData('itemId', 'new');
		$name =  htmlspecialchars(tra($this->getConfiguration('name')));

		$data_toggle = '';
		if ($prefs['javascript_enabled'] === 'y' && ($toggle === 'o' || $toggle === 'c')) {
			$class = ' ' . ($toggle === 'c' ? 'trackerHeaderClose' : 'trackerHeaderOpen');
			$data_toggle = 'data-toggle="' . $toggle . '"';
			$js = "\$('#$id').click(function(event){";
			
		
		}
		if ($inTable) {
			$js = '
(function() {
	var processTableForHeaders = function( $table ) {
		var $hdr, $newtable = $("<table>").attr("class", $table.attr("class"));
		$("tr", $table).each(function() {	// step through each row
			if ($(".hdrField", this).length) {	// chop the table...
				var $this = $(this);
				var $sibs = $this.nextAll("tr");
				var level = $(".hdrField:first", this).data("level");
				var name = $(".formlabel:first", this).text();
				$hdr = $("<h" + level + ">").text($.trim(name));
				var toggle = $(".hdrField:first", this).data("toggle");
				if (toggle) {
					$hdr.click(function(){
						$newtable.toggle();
						$(this).toggleClass("trackerHeaderClose")
								.toggleClass("trackerHeaderOpen");
					}).addClass(toggle === "c" ? "trackerHeaderClose" : "trackerHeaderOpen")
				}
				$sibs.each(function(){
					$newtable.append(this);
					$this.remove();
				});
				return false;
			}
		});
		$table.after($newtable).after($hdr);
		if ($("tr", $newtable).length) {
			processTableForHeaders($newtable);	// recurse until done
		}
	}
	processTableForHeaders($(".hdrField").parents("table:first"));
})();';
		} else {
			$js = '';	// TODO div mode for plugins or something
		}
		$headerlib->add_jq_onready($js);
		
		// just a marker for jQ to find
		$html = '<span id=' . $id . ' class="hdrField' . $class . '" data-level="' . $level . '" ' .
				$data_toggle .' style="display:none;"></span>';
		
		return $html;
	}
}

