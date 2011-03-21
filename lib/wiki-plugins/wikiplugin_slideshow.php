<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

/* Tiki-Wiki plugin example 
 *
 * This is an example plugin to let you know how to create
 * a plugin. Plugins are called using the syntax
 * {NAME(params)}content{NAME}
 * Name must be in uppercase!
 * params is in the form: name=>value,name2=>value2 (don't use quotes!)
 * If the plugin doesn't use params use {NAME()}content{NAME}
 *
 * The function will receive the plugin content in $data and the params
 * in the asociative array $params (using extract to pull the arguments
 * as in the example is a good practice)
 * The function returns some text that will replace the content in the
 * wiki page.
 */
function wikiplugin_slideshow_help() {
	return tra("SLIDESHOW").':<br />~np~{SLIDESHOW(backgroundurl=>x, class=>c)}'.tra("Slideshow Notes").'{SLIDESHOW}~/np~';
}

function wikiplugin_slideshow_info() {
	return array(
		'name' => tra('SLIDESHOW'),
		'documentation' => 'Slideshow',
		'description' => tra('Configure a slideshow.  Extends the existing wiki page slideshow with notes & styles.'),
		'prefs' => array( 'feature_slideshow' ),
		'body' => tra('Slideshow notes - Separate with "/////"'),
		'icon' => 'pics/icons/images.png',
		'params' => array(
			'theme' => array(
				'required' => false,
				'name' => tra('Theme'),
				'description' => tra('The side on which you would like to display text if there are images that are resized or present'),
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
				'accepted' => tra('Any html color'),
				'default' => '#56D0FF',
				'since' => '7.0',
			),
			'headerbackgroundcolor' => array(
				'required' => false,
				'name' => tra('Header background color'),
				'description' => tra('Apply a color to the headers of your slideshow'),
				'filter' => 'text',
				'accepted' => tra('Any html color'),
				'since' => '7.0',
			),
			'slidefontcolor' => array(
				'required' => false,
				'name' => tra('Slide font color'),
				'description' => tra('Apply a color to the slides of your slideshow'),
				'filter' => 'text',
				'accepted' => tra('Any html color'),
				'default' => '#EEFAFF',
				'since' => '7.0',
			),
			'listitemhighlightcolor' => array(
				'required' => false,
				'name' => tra('Line Item highlight color'),
				'description' => tra('Apply a color to the line item when mouse over'),
				'filter' => 'text',
				'accepted' => tra('Any html color'),
				'default' => '',
				'since' => '7.0',
			),
			'slideseconds' => array(
				'required' => false,
				'name' => tra('Slide Seconds'),
				'description' => tra('How many seconds a slide will be open while playing'),
				'filter' => 'digits',
				'accepted' => tra('Second count'),
				'default' => '15',
				'since' => '7.0'
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

function getSlideshowTheme($theme, $makeJson) {
	global $prefs;

	$result = array();
	//this makes it so that any input so long as the characters are the same can be used
	$theme = strtolower($theme);
	$theme = str_replace(' ', '', $theme);
	$theme = ($theme == 'default' ? $prefs['feature_jquery_ui_theme'] : $theme);
	
	$result['themename'] = $theme;
	switch ($theme) {
		case "ui-lightness":
			$result['backgroundColor'] = '#F6A828';
			$result['backgroundImage'] = 'lib/jquery/jquery.s5/images/bg.png';
			$result['headerFontColor'] = '#1C94C4';
			$result['headerBackgroundColor'] = '';
			$result['slideFontColor'] = '#333';
			$result['listItemHighlightColor'] = '#363636';
			break;
		case "ui-darkness":
			$result['backgroundColor'] = '#333';
			$result['backgroundImage'] = 'lib/jquery/jquery.s5/images/bg.png';
			$result['headerFontColor'] = 'white';
			$result['headerBackgroundColor'] = '';
			$result['slideFontColor'] = 'white';
			$result['listItemHighlightColor'] = '#1C94C4';
			break;
		case "smoothness": 
			$result['backgroundColor'] = '#E6E6E6';
			$result['backgroundImage'] = 'lib/jquery/jquery.s5/images/bg.png';
			$result['headerFontColor'] = '#212121';
			$result['headerBackgroundColor'] = '';
			$result['slideFontColor'] = '#222';
			$result['listItemHighlightColor'] = '';
			break;
		case "start":
			$result['backgroundColor'] = '#2191c0';
			$result['backgroundImage'] = 'lib/jquery/jquery.s5/images/bg.png';
			$result['headerFontColor'] = '#acdd4a';
			$result['headerBackgroundColor'] = '';
			$result['slideFontColor'] = 'white';
			$result['listItemHighlightColor'] = '#77D5F7';
			break;
		case "redmond": 
			$result['backgroundColor'] = '#C5DBEC';
			$result['backgroundImage'] = 'lib/jquery/jquery.s5/images/bg.png';
			$result['headerFontColor'] = '#2E6E9E';
			$result['headerBackgroundColor'] = '';
			$result['slideFontColor'] = '#222';
			$result['listItemHighlightColor'] = '#E17009';
			break;
		case "sunny": 
			$result['backgroundColor'] = '#FEEEBD';
			$result['backgroundImage'] = 'lib/jquery/jquery.s5/images/bg.png';
			$result['headerFontColor'] = '#0074C7';
			$result['headerBackgroundColor'] = '';
			$result['slideFontColor'] = '#383838';
			$result['listItemHighlightColor'] = '#4C3000';
			break;
		case "overcast": 
			$result['backgroundColor'] = '#C9C9C9';
			$result['backgroundImage'] = 'lib/jquery/jquery.s5/images/bg.png';
			$result['headerFontColor'] = '#212121';
			$result['headerBackgroundColor'] = '';
			$result['slideFontColor'] = '#333';
			$result['listItemHighlightColor'] = '#599FCF';
			break;
		case "le-frog": 
			$result['backgroundColor'] = '#285C00';
			$result['backgroundImage'] = 'lib/jquery/jquery.s5/images/bg.png';
			$result['headerFontColor'] = 'white';
			$result['headerBackgroundColor'] = '';
			$result['slideFontColor'] = 'white';
			$result['listItemHighlightColor'] = '#F9DD34';
			break;
		case "flick": 
			$result['backgroundColor'] = '#DDD';
			$result['backgroundImage'] = 'lib/jquery/jquery.s5/images/bg.png';
			$result['headerFontColor'] = '#0073EA';
			$result['headerBackgroundColor'] = '';
			$result['slideFontColor'] = '#444';
			$result['listItemHighlightColor'] = '#FF0084';
			break;
		case "pepper-grinder": 
			$result['backgroundColor'] = '#ECEADF';
			$result['backgroundImage'] = 'lib/jquery/jquery.s5/images/bg.png';
			$result['headerFontColor'] = '#654B24';
			$result['headerBackgroundColor'] = '';
			$result['slideFontColor'] = '#1F1F1F';
			$result['listItemHighlightColor'] = '#B83400';
			break;
		case "eggplant": 
			$result['backgroundColor'] = '#3D3644';
			$result['backgroundImage'] = 'lib/jquery/jquery.s5/images/bg.png';
			$result['headerFontColor'] = 'white';
			$result['headerBackgroundColor'] = '';
			$result['slideFontColor'] = 'white';
			$result['listItemHighlightColor'] = '#FFDB1F';
			break;
		case "dark-hive": 
			$result['backgroundColor'] = '#444';
			$result['backgroundImage'] = 'lib/jquery/jquery.s5/images/bg.png';
			$result['headerFontColor'] = '#0972A5';
			$result['headerBackgroundColor'] = '';
			$result['slideFontColor'] = 'white';
			$result['listItemHighlightColor'] = '#2E7DB2';
			break;
		case "cupertino": 
			$result['backgroundColor'] = '#D7EBF9';
			$result['backgroundImage'] = 'lib/jquery/jquery.s5/images/bg.png';
			$result['headerFontColor'] = '#2694E8';
			$result['headerBackgroundColor'] = '';
			$result['slideFontColor'] = '#362B36';
			$result['listItemHighlightColor'] = '#2694E8';
			break;
		case "south-street": 
			$result['backgroundColor'] = '#F5F3E5';
			$result['backgroundImage'] = 'lib/jquery/jquery.s5/images/bg.png';
			$result['headerFontColor'] = '#459E00';
			$result['headerBackgroundColor'] = '';
			$result['slideFontColor'] = '#312E25';
			$result['listItemHighlightColor'] = '#459E00';
			break;
		case "blitzer": 
			$result['backgroundColor'] = '#EEE';
			$result['backgroundImage'] = 'lib/jquery/jquery.s5/images/bg.png';
			$result['headerFontColor'] = '#C00';
			$result['headerBackgroundColor'] = '';
			$result['slideFontColor'] = '#333';
			$result['listItemHighlightColor'] = '#004276';
			break;
		case "humanity": 
			$result['backgroundColor'] = '#EDE4D4';
			$result['backgroundImage'] = 'lib/jquery/jquery.s5/images/bg.png';
			$result['headerFontColor'] = '#B85700';
			$result['headerBackgroundColor'] = '';
			$result['slideFontColor'] = '#1E1B1D';
			$result['listItemHighlightColor'] = '#592003';
			break;
		case "hot-sneaks": 
			$result['backgroundColor'] = '#35414F';
			$result['backgroundImage'] = 'lib/jquery/jquery.s5/images/bg.png';
			$result['headerFontColor'] = '#E1E463';
			$result['headerBackgroundColor'] = '';
			$result['slideFontColor'] = '#93C3CD';
			$result['listItemHighlightColor'] = '#DB4865';
			break;
		case "excite-bike": 
			$result['backgroundColor'] = '#EEE';
			$result['backgroundImage'] = 'lib/jquery/jquery.s5/images/bg.png';
			$result['headerFontColor'] = '#E69700';
			$result['headerBackgroundColor'] = '';
			$result['slideFontColor'] = '#1484E6';
			$result['listItemHighlightColor'] = '#2293F7';
			break;
		case "vader": 
			$result['backgroundColor'] = '#121212';
			$result['backgroundImage'] = 'lib/jquery/jquery.s5/images/bg.png';
			$result['headerFontColor'] = '#ADADAD';
			$result['headerBackgroundColor'] = '';
			$result['slideFontColor'] = '#EEE';
			$result['listItemHighlightColor'] = '#ADADAD';
			break;
		case "dot-luv": 
			$result['backgroundColor'] = '#111';
			$result['backgroundImage'] = 'lib/jquery/jquery.s5/images/bg.png';
			$result['headerFontColor'] = '#0b3e6f';
			$result['headerBackgroundColor'] = '';
			$result['slideFontColor'] = '#D9D9D9';
			$result['listItemHighlightColor'] = '#0b58a2';
			break;
		case "mint-choc": 
			$result['backgroundColor'] = '#453326';
			$result['backgroundImage'] = 'lib/jquery/jquery.s5/images/bg.png';
			$result['headerFontColor'] = '#BAEC7E';
			$result['headerBackgroundColor'] = '';
			$result['slideFontColor'] = '#ffffff';
			$result['listItemHighlightColor'] = '#619226';
			break;
		case "black-tie": 
			$result['backgroundColor'] = '#333333';
			$result['backgroundImage'] = 'lib/jquery/jquery.s5/images/bg.png';
			$result['headerFontColor'] = '#a3a3a3';
			$result['headerBackgroundColor'] = '';
			$result['slideFontColor'] = '#eeeeee';
			$result['listItemHighlightColor'] = '#ffeb80';
			break;
		case "trontastic": 
			$result['backgroundColor'] = '#222222';
			$result['backgroundImage'] = 'lib/jquery/jquery.s5/images/bg.png';
			$result['headerFontColor'] = '#9fda58';
			$result['headerBackgroundColor'] = '';
			$result['slideFontColor'] = '#ffffff';
			$result['listItemHighlightColor'] = '#f1fbe5';
			break;
		case "swanky-purse": 
			$result['backgroundColor'] = '#261803';
			$result['backgroundImage'] = 'lib/jquery/jquery.s5/images/bg.png';
			$result['headerFontColor'] = '#eacd86';
			$result['headerBackgroundColor'] = '';
			$result['slideFontColor'] = '#efec9f';
			$result['listItemHighlightColor'] = '#d5ac5d';
			break;
	}
	
	if ($makeJson) return json_encode($result);
	
	return $result;
}

function wikiplugin_slideshow($data, $params) {
	global $dbTiki, $tiki_p_admin, $prefs, $user, $page, $tikilib, $smarty;
	extract ($params,EXTR_SKIP);

	$theme = (isset($theme) ? $theme : 'default');
	$themeName = '';
	
	$backgroundcolor = (isset($backgroundcolor) ? $backgroundcolor : '');
	$backgroundurl = (isset($backgroundurl) ? $backgroundurl : '');
	$headerfontcolor = (isset($headerfontcolor) ? $headerfontcolor : '');
	$headerbackgroundcolor = (isset($headerbackgroundcolor) ? $headerbackgroundcolor : '');
	$slidefontcolor = (isset($slidefontcolor) ? $slidefontcolor : '');
	$listitemhighlightcolor = (isset($listitemhighlightcolor) ? $listitemhighlightcolor : '');
	$class = (isset($class) ? " $class"  : '');
	$slideduration = (isset($slideseconds) ? $slideseconds : 15) * 1000;
	$textside = (isset($textside) ? $textside : 'left');
	
	if ($theme) {
		$theme = getSlideshowTheme($theme);
		$backgroundcolor = ($backgroundcolor ? $backgroundcolor : $theme['backgroundColor']);
		$backgroundurl = ($backgroundurl ? $backgroundurl : $theme['backgroundImage']);
		$headerfontcolor = ($headerfontcolor ? $headerfontcolor : $theme['headerFontColor']);
		$headerbackgroundcolor = ($headerbackgroundcolor ? $headerbackgroundcolor : $theme['headerBackgroundColor']);
		$slidefontcolor = ($slidefontcolor ? $slidefontcolor : $theme['slideFontColor']);
		$listitemhighlightcolor = ($listitemhighlightcolor ? $listitemhighlightcolor : $theme['listItemHighlightColor']);
		$themeName = $theme['themename'];
	}
	
	$notes = explode("/////", ($data ? $data : ""));
	$notesHtml = '';
	foreach ( $notes as $note ) {
		$notesHtml .= '<span class="s5-note">'.$note.'</span>';
	}
	
	global $headerlib;
	
	$headerlib->add_js("
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
			themeName: '$themeName'
		};
	");
	
	return "~np~<div id='' class='tiki_slideshow'>$notesHtml</div>~/np~";
}
