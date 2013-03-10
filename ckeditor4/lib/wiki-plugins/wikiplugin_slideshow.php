<?php
// (c) Copyright 2002-2013 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function wikiplugin_slideshow_info()
{
	return array(
		'name' => tra('Slideshow'),
		'documentation' => 'Slideshow',
		'description' => tra('Configure a slideshow. Extends the existing wiki page slideshow with notes & styles.'),
		'prefs' => array( 'wikiplugin_slideshow', 'feature_slideshow' ),
		'body' => tra('Slideshow notes - Separate with "/////"'),
		'icon' => 'img/icons/images.png',
		'tags' => array( 'basic' ),
		'params' => array(
			'theme' => array(
				'required' => false,
				'name' => tra('Theme'),
				'description' => tra('The theme you want to use for the slideshow, default will be what you choose from the admin panel under Look and Feel for jQuery UI'),
				'filter' => 'text',
				'default' => tra('Tiki jQuery UI Theme'),
				'since' => '7.0',
				'options' => array(
					array('text' => 'ui-lightness', 'value' => 'ui-lightness'),
					array('text' => 'ui-darkness', 'value' => 'ui-darkness'),
					array('text' => 'smoothness', 'value' => 'smoothness'),
					array('text' => 'start', 'value' => 'start'),
					array('text' => 'redmond', 'value' => 'redmond'),
					array('text' => 'sunny', 'value' => 'sunny'),
					array('text' => 'overcast', 'value' => 'overcast'),
					array('text' => 'le-frog', 'value' => 'le-frog'),
					array('text' => 'flick', 'value' => 'flick'),
					array('text' => 'pepper Grinder', 'value' => 'pepper-grinder'),
					array('text' => 'eggplant', 'value' => 'eggplant'),
					array('text' => 'dark-hive', 'value' => 'dark-hive'),
					array('text' => 'cupertino', 'value' => 'cupertino'),
					array('text' => 'south-street', 'value' => 'south-street'),
					array('text' => 'blitzer', 'value' => 'blitzer'),
					array('text' => 'humanity', 'value' => 'humanity'),
					array('text' => 'hot-sneaks', 'value' => 'hot-sneaks'),
					array('text' => 'excite-bike', 'value' => 'excite-bike'),
					array('text' => 'vader', 'value' => 'vader'),
					array('text' => 'dot-Luv', 'value' => 'dot-luv'),
					array('text' => 'mint-choc', 'value' => 'mint-shoc'),
					array('text' => 'black-tie', 'value' => 'black-tie'),
					array('text' => 'trontastic', 'value' => 'trontastic'),
					array('text' => 'swanky-purse', 'value' => 'swanky-purse'),
				),
			),
			'backgroundurl' => array(
				'required' => false,
				'name' => tra('Background Url Location'),
				'description' => tra('URL of the background image to use in your slideshow, overrides backgroundcolor'),
				'filter' => 'url',
				'accepted' => tra('Valid url'),
				'default' => '',
				'since' => '7.0',
			),
			'backgroundcolor' => array(
				'required' => false,
				'name' => tra('Background Color'),
				'description' => tra('Background color to use in your slideshow, default '),
				'default' => '#0087BB',
				'since' => '7.0'
			),
			'class' => array(
				'required' => false,
				'name' => tra('CSS Class'),
				'description' => tra('Apply custom CSS class to the containing div.'),
				'filter' => 'text',
				'accepted' => tra('Any valid CSS class'),
				'default' => '',
				'since' => '7.0',
			),
			'headerfontcolor' => array(
				'required' => false,
				'name' => tra('Header font color'),
				'description' => tra('Apply a color to the headers of your slideshow'),
				'filter' => 'text',
				'accepted' => tra('Any HTML color'),
				'default' => '#56D0FF',
				'since' => '7.0',
			),
			'headerbackgroundcolor' => array(
				'required' => false,
				'name' => tra('Header background color'),
				'description' => tra('Apply a color to the headers of your slideshow'),
				'filter' => 'text',
				'accepted' => tra('Any HTML color'),
				'since' => '7.0',
			),
			'slidefontcolor' => array(
				'required' => false,
				'name' => tra('Slide font color'),
				'description' => tra('Apply a color to the slides of your slideshow'),
				'filter' => 'text',
				'accepted' => tra('Any HTML color'),
				'default' => '#EEFAFF',
				'since' => '7.0',
			),
			'listitemhighlightcolor' => array(
				'required' => false,
				'name' => tra('Line Item highlight color'),
				'description' => tra('Apply a color to the line item when mouse over'),
				'filter' => 'text',
				'accepted' => tra('Any HTML color'),
				'default' => '',
				'since' => '7.0',
			),
			'slideseconds' => array(
				'required' => false,
				'name' => tra('Slide Seconds'),
				'description' => tra('How many seconds a slide will be open while playing, overridden when slideduration is set.'),
				'filter' => 'digits',
				'accepted' => tra('Second count'),
				'default' => '15',
				'since' => '7.0'
			),
			'slideduration' => array(
				'required' => false,
				'name' => tra('Slide Milliseconds'),
				'description' => tra('How many milliseconds a slide will be open while playing'),
				'filter' => 'digits',
				'accepted' => tra('Second count'),
				'default' => '15000',
				'since' => '9.0'
			),
			'textside' => array(
				'required' => false,
				'name' => tra('Text Side'),
				'description' => tra('The side on which you would like to display text if there are images that are resized or present'),
				'filter' => 'text',
				'default' => tra('Left'),
				'since' => '7.0',
				'options' => array(
					array('text' => tra('Left'), 'value' => 'left'),
					array('text' => tra('Right'), 'value' => 'right'),
				),
			),
		),
	);
}

function wikiplugin_slideshow($data, $params)
{
	global $dbTiki, $tiki_p_admin, $prefs, $user, $page, $tikilib, $smarty;
	extract($params, EXTR_SKIP);

	$theme = (isset($theme) ? $theme : 'default');
	$themeName = '';

	$backgroundcolor = (isset($backgroundcolor) ? $backgroundcolor : '');
	$backgroundurl = (isset($backgroundurl) ? $backgroundurl : '');
	$headerfontcolor = (isset($headerfontcolor) ? $headerfontcolor : '');
	$headerbackgroundcolor = (isset($headerbackgroundcolor) ? $headerbackgroundcolor : '');
	$slidefontcolor = (isset($slidefontcolor) ? $slidefontcolor : '');
	$listitemhighlightcolor = (isset($listitemhighlightcolor) ? $listitemhighlightcolor : '');
	$class = (isset($class) ? " $class"  : '');

	if (!isset($slideduration)) {
		$slideduration = (isset($slideseconds) ? $slideseconds : 15) * 1000;
	}

	$textside = (isset($textside) ? $textside : 'left');

	if ($theme) {
		$theme = $tikilib->getSlideshowTheme($theme);
		$backgroundcolor = ($backgroundcolor ? $backgroundcolor : $theme['backgroundColor']);
		$backgroundurl = ($backgroundurl ? $backgroundurl : $theme['backgroundImage']);
		$headerfontcolor = ($headerfontcolor ? $headerfontcolor : $theme['headerFontColor']);
		$headerbackgroundcolor = ($headerbackgroundcolor ? $headerbackgroundcolor : $theme['headerBackgroundColor']);
		$slidefontcolor = ($slidefontcolor ? $slidefontcolor : $theme['slideFontColor']);
		$listitemhighlightcolor = ($listitemhighlightcolor ? $listitemhighlightcolor : $theme['listItemHighlightColor']);
		$themeName = $theme['themeName'];
	}

	$notes = explode("/////", ($data ? $data : ""));
	$notesHtml = '';
	foreach ( $notes as $note ) {
		$notesHtml .= '<span class="s5-note">'.$note.'</span>';
	}

	global $headerlib;

	$headerlib->add_js(
		"window.slideshowSettings = {
			class: '$class',
			backgroundurl: '$backgroundurl',
			backgroundcolor: '$backgroundcolor',
			headerfontcolor: '$headerfontcolor',
			headerbackgroundcolor: '$headerbackgroundcolor',
			slidefontcolor: '$slidefontcolor',
			slideduration: $slideduration,
			listitemhighlightcolor: '$listitemhighlightcolor',
			textside: '$textside',
			theme: '$themeName'
		};

		window.s5Settings = {
			slideClass: '$class',
			backgroundImage: '$backgroundurl',
			backgroundColor: '$backgroundcolor',
			headerFontColor: '$headerfontcolor',
			headerBackgroundColor: '$headerbackgroundcolor',
			slideFontColor: '$slidefontcolor',
			slideDuration: $slideduration,
			listItemHighlightColor: '$listitemhighlightcolor',
			textSide: '$textside',
			themeName: '$themeName',
			basePath: 'lib/jquery.s5/'
		};"
	);

	return "~np~<div id='' class='tiki_slideshow'>$notesHtml</div>~/np~";
}
