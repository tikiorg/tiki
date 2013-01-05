<?php
// (c) Copyright 2002-2013 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function wikiplugin_wysiwyg_info()
{
	return array(
		'name' => 'WYSIWYG',
		'documentation' => 'PluginWYSIWYG',
		'description' => tra('Permits to have a WYSIWYG section for part of a page.'),
		'prefs' => array('wikiplugin_wysiwyg'),
		'params' => array(),
		'icon' => 'img/icons/mime/default.png',
		'tags' => array( 'experimental' ),
		'filter' => 'purifier',			/* N.B. uses htmlpurifier to ensure only "clean" html gets in */
		'body' => tra('Content'),
		'extraparams' => true,
		'params' => array(
			'width' => array(
				'required' => false,
				'name' => tra('Width'),
				'description' => tra('Minimum width for DIV. Default:500px'),
				'filter' => 'text',
				'default' => '500px',
			),
			'height' => array(
				'required' => false,
				'name' => tra('Height'),
				'description' => tra('Minimum height for DIV. Default:300px.'),
				'filter' => 'text',
				'default' => '300px',
			),
		),
	);
} // wikiplugin_wysiwyg_info()


function wikiplugin_wysiwyg($data, $params)
{
	global $wysiwyglib; include_once('lib/ckeditor_tiki/wysiwyglib.php');

	// TODO refactor: defaults for plugins?
	$defaults = array();
	$plugininfo = wikiplugin_wysiwyg_info();
	foreach ($plugininfo['params'] as $key => $param) {
		$defaults["$key"] = $param['default'];
	}
	$params = array_merge($defaults, $params);

	$html = TikiLib::lib('tiki')->parse_data($data, array('is_html' => true));

	global $tiki_p_edit, $page, $prefs;
	static $execution = 0;

	if ($tiki_p_edit === 'y') {
		$class = "wp_wysiwyg";
		$exec_key = $class . '_' . ++ $execution;
		$style = " style='min-width:{$params['width']};min-height:{$params['height']}'";

		$params['section'] = empty($params['section']) ? 'wysiwyg_plugin' : $params['section'];
		$params['_wysiwyg'] = 'y';
		$params['is_html'] = true;
		//$params['comments'] = true;
		$ckoption = $wysiwyglib->setUpEditor(true, $exec_key, $params, '', false);

		$html = "<div id='$exec_key' class='{$class}'$style>" . $html . '</div>';

		$js = '

$("#' . $exec_key . '").each(function(){
	var wp_bgcol = $(this).css("background-color");
	$(this).mouseover(function(){
		$(this).css({
			backgroundColor: "#ddd",
			cusor: "crosshair"
		});

	}).mouseout(function(){
		$(this).css({
			backgroundColor: wp_bgcol,
			cusor: "inherit"
		});
	}).click(function(){
		var $this = $(this);
		// TODO set modal somehow?
		//$("body *:not(#" + $(this).attr("id") + ")").css({backgroundColor: "#ddd"});

		var ok = true;
		$(".' . $class . ':not(#' . $exec_key . ')").each(function () {
			if (CKEDITOR.instances[$(this).attr("id")]) {
				if (CKEDITOR.instances[$(this).attr("id")].mayBeDirty) {
					if (confirm(tr("You have unsaved changes in this WYSIWYG section.\nDo you want to save your changes?"))) {
						CKEDITOR.instances[$(this).attr("id")].focus();
						ok = false;
						return;
					}
				}
				CKEDITOR.instances[$(this).attr("id")].destroy();
			}
			$(".button_" + $(this).attr("id")).remove();
		});
		if (!ok) {
			return;
		}

		$this.ckeditor(function() {
			// close others
			var editor = CKEDITOR.instances[$this.attr("id")];

			var editorSelector = "#cke_" + this.element.getId();

			$(editorSelector).after(
				$("<button class=\"button_' . $exec_key . '\">" + tr("Cancel") + "</button>").button()
					.click(function() {
						$(".button_' . $exec_key . '").remove();
						editor.destroy();
					})
			).after(
				$("<button class=\"button_' . $exec_key . '\">" + tr("Save") + "</button>").button()
					.click(function(event) {
						var data = editor.getData();
						$(editorSelector).modal(tr("Saving..."));

						$.post("tiki-wikiplugin_edit.php", {
							page: "' . $page . '",
							type: "wysiwyg",
							message: "Modified by WYSIWYG Plugin",
							index: ' . $execution . ',
							content: data
						}, function() {
							location.reload();
						});
						return false;
					})
			);
		}' . (!empty($ckoption) ? ', ' . $ckoption : '') .'
		);
	});
});
';
		TikiLib::lib('header')->add_jq_onready($js);
	}
	return '~np~' . $html . '~/np~';

}

