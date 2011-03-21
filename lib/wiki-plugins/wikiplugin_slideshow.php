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
				'default' => tra('UI lightness'),
				'since' => '7.0',
				'options' => array(					
					array('text' => 'UI lightness', 'value' => 'UI lightness'),
					array('text' => 'UI darkness', 'value' => 'UI darkness'),
					array('text' => 'Smoothness', 'value' => 'Smoothness'),
					array('text' => 'Start', 'value' => 'Start'),
					array('text' => 'Redmond', 'value' => 'Redmond'),
					array('text' => 'Sunny', 'value' => 'Sunny'),
					array('text' => 'Overcast', 'value' => 'Overcast'),
					array('text' => 'Le Frog', 'value' => 'Le Frog'),
					array('text' => 'Flick', 'value' => 'Flick'),
					array('text' => 'Pepper Grinder', 'value' => 'Pepper Grinder'),
					array('text' => 'Eggplant', 'value' => 'Eggplant'),
					array('text' => 'Dark Hive', 'value' => 'Dark Hive'),
					array('text' => 'Cupertino', 'value' => 'Cupertino'),
					array('text' => 'South Street', 'value' => 'South Street'),
					array('text' => 'Blitzer', 'value' => 'Blitzer'),
					array('text' => 'Humanity', 'value' => 'Humanity'),
					array('text' => 'Hot sneaks', 'value' => 'Hot sneaks'),
					array('text' => 'Excite Bike', 'value' => 'Excite Bike'),
					array('text' => 'Vader', 'value' => 'Vader'),
					array('text' => 'Dot Luv', 'value' => 'Dot Luv'),
					array('text' => 'Mint Choc', 'value' => 'Mint Choc'),
					array('text' => 'Black Tie', 'value' => 'Black Tie'),
					array('text' => 'Trontastic', 'value' => 'Trontastic'),
					array('text' => 'Swanky Purse', 'value' => 'Swanky Purse'),
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
	$result = array();
	
	//this makes it so that any input so long as the characters are the same can be used
	$theme = strtolower($theme);
	$theme = str_replace(' ', '', $theme);
	
	switch ($theme) {
		case "uilightness":
			$result['backgroundcolor'] = '#F6A828';
			$result['backgroundurl'] = 'lib/jquery/jquery.s5/images/bg.png';
			$result['headerfontcolor'] = '#1C94C4';
			$result['headerbackgroundcolor'] = '';
			$result['slidefontcolor'] = '#333';
			$result['listitemhighlightcolor'] = '#363636';
			break;
		case "uidarkness":
			$result['backgroundcolor'] = '#333';
			$result['backgroundurl'] = 'lib/jquery/jquery.s5/images/bg.png';
			$result['headerfontcolor'] = 'white';
			$result['headerbackgroundcolor'] = '';
			$result['slidefontcolor'] = 'white';
			$result['listitemhighlightcolor'] = '#1C94C4';
			break;
		case "smoothness": 
			$result['backgroundcolor'] = '#E6E6E6';
			$result['backgroundurl'] = 'lib/jquery/jquery.s5/images/bg.png';
			$result['headerfontcolor'] = '#212121';
			$result['headerbackgroundcolor'] = '';
			$result['slidefontcolor'] = '#222';
			$result['listitemhighlightcolor'] = '';
			break;
		case "start":
			$result['backgroundcolor'] = '#2191c0';
			$result['backgroundurl'] = 'lib/jquery/jquery.s5/images/bg.png';
			$result['headerfontcolor'] = '#acdd4a';
			$result['headerbackgroundcolor'] = '';
			$result['slidefontcolor'] = 'white';
			$result['listitemhighlightcolor'] = '#77D5F7';
			break;
		case "redmond": 
			$result['backgroundcolor'] = '#C5DBEC';
			$result['backgroundurl'] = 'lib/jquery/jquery.s5/images/bg.png';
			$result['headerfontcolor'] = '#2E6E9E';
			$result['headerbackgroundcolor'] = '';
			$result['slidefontcolor'] = '#222';
			$result['listitemhighlightcolor'] = '#E17009';
			break;
		case "sunny": 
			$result['backgroundcolor'] = '#FEEEBD';
			$result['backgroundurl'] = 'lib/jquery/jquery.s5/images/bg.png';
			$result['headerfontcolor'] = '#0074C7';
			$result['headerbackgroundcolor'] = '';
			$result['slidefontcolor'] = '#383838';
			$result['listitemhighlightcolor'] = '#4C3000';
			break;
		case "overcast": 
			$result['backgroundcolor'] = '#C9C9C9';
			$result['backgroundurl'] = 'lib/jquery/jquery.s5/images/bg.png';
			$result['headerfontcolor'] = '#212121';
			$result['headerbackgroundcolor'] = '';
			$result['slidefontcolor'] = '#333';
			$result['listitemhighlightcolor'] = '#599FCF';
			break;
		case "lefrog": 
			$result['backgroundcolor'] = '#285C00';
			$result['backgroundurl'] = 'lib/jquery/jquery.s5/images/bg.png';
			$result['headerfontcolor'] = 'white';
			$result['headerbackgroundcolor'] = '';
			$result['slidefontcolor'] = 'white';
			$result['listitemhighlightcolor'] = '#F9DD34';
			break;
		case "flick": 
			$result['backgroundcolor'] = '#DDD';
			$result['backgroundurl'] = 'lib/jquery/jquery.s5/images/bg.png';
			$result['headerfontcolor'] = '#0073EA';
			$result['headerbackgroundcolor'] = '';
			$result['slidefontcolor'] = '#444';
			$result['listitemhighlightcolor'] = '#FF0084';
			break;
		case "peppergrinder": 
			$result['backgroundcolor'] = '#ECEADF';
			$result['backgroundurl'] = 'lib/jquery/jquery.s5/images/bg.png';
			$result['headerfontcolor'] = '#654B24';
			$result['headerbackgroundcolor'] = '';
			$result['slidefontcolor'] = '#1F1F1F';
			$result['listitemhighlightcolor'] = '#B83400';
			break;
		case "eggplant": 
			$result['backgroundcolor'] = '#3D3644';
			$result['backgroundurl'] = 'lib/jquery/jquery.s5/images/bg.png';
			$result['headerfontcolor'] = 'white';
			$result['headerbackgroundcolor'] = '';
			$result['slidefontcolor'] = 'white';
			$result['listitemhighlightcolor'] = '#FFDB1F';
			break;
		case "darkhive": 
			$result['backgroundcolor'] = '#444';
			$result['backgroundurl'] = 'lib/jquery/jquery.s5/images/bg.png';
			$result['headerfontcolor'] = '#0972A5';
			$result['headerbackgroundcolor'] = '';
			$result['slidefontcolor'] = 'white';
			$result['listitemhighlightcolor'] = '#2E7DB2';
			break;
		case "cupertino": 
			$result['backgroundcolor'] = '#D7EBF9';
			$result['backgroundurl'] = 'lib/jquery/jquery.s5/images/bg.png';
			$result['headerfontcolor'] = '#2694E8';
			$result['headerbackgroundcolor'] = '';
			$result['slidefontcolor'] = '#362B36';
			$result['listitemhighlightcolor'] = '#2694E8';
			break;
		case "southstreet": 
			$result['backgroundcolor'] = '#F5F3E5';
			$result['backgroundurl'] = 'lib/jquery/jquery.s5/images/bg.png';
			$result['headerfontcolor'] = '#459E00';
			$result['headerbackgroundcolor'] = '';
			$result['slidefontcolor'] = '#312E25';
			$result['listitemhighlightcolor'] = '#459E00';
			break;
		case "blitzer": 
			$result['backgroundcolor'] = '#EEE';
			$result['backgroundurl'] = 'lib/jquery/jquery.s5/images/bg.png';
			$result['headerfontcolor'] = '#C00';
			$result['headerbackgroundcolor'] = '';
			$result['slidefontcolor'] = '#333';
			$result['listitemhighlightcolor'] = '#004276';
			break;
		case "humanity": 
			$result['backgroundcolor'] = '#EDE4D4';
			$result['backgroundurl'] = 'lib/jquery/jquery.s5/images/bg.png';
			$result['headerfontcolor'] = '#B85700';
			$result['headerbackgroundcolor'] = '';
			$result['slidefontcolor'] = '#1E1B1D';
			$result['listitemhighlightcolor'] = '#592003';
			break;
		case "hotsneaks": 
			$result['backgroundcolor'] = '#35414F';
			$result['backgroundurl'] = 'lib/jquery/jquery.s5/images/bg.png';
			$result['headerfontcolor'] = '#E1E463';
			$result['headerbackgroundcolor'] = '';
			$result['slidefontcolor'] = '#93C3CD';
			$result['listitemhighlightcolor'] = '#DB4865';
			break;
		case "excitebike": 
			$result['backgroundcolor'] = '#EEE';
			$result['backgroundurl'] = 'lib/jquery/jquery.s5/images/bg.png';
			$result['headerfontcolor'] = '#E69700';
			$result['headerbackgroundcolor'] = '';
			$result['slidefontcolor'] = '#1484E6';
			$result['listitemhighlightcolor'] = '#2293F7';
			break;
		case "vader": 
			$result['backgroundcolor'] = '#121212';
			$result['backgroundurl'] = 'lib/jquery/jquery.s5/images/bg.png';
			$result['headerfontcolor'] = '#ADADAD';
			$result['headerbackgroundcolor'] = '';
			$result['slidefontcolor'] = '#EEE';
			$result['listitemhighlightcolor'] = '#ADADAD';
			break;
		case "dotluv": 
			$result['backgroundcolor'] = '#111';
			$result['backgroundurl'] = 'lib/jquery/jquery.s5/images/bg.png';
			$result['headerfontcolor'] = '#0b3e6f';
			$result['headerbackgroundcolor'] = '';
			$result['slidefontcolor'] = '#D9D9D9';
			$result['listitemhighlightcolor'] = '#0b58a2';
			break;
		case "mintchoc": 
			$result['backgroundcolor'] = '#453326';
			$result['backgroundurl'] = 'lib/jquery/jquery.s5/images/bg.png';
			$result['headerfontcolor'] = '#BAEC7E';
			$result['headerbackgroundcolor'] = '';
			$result['slidefontcolor'] = '#ffffff';
			$result['listitemhighlightcolor'] = '#619226';
			break;
		case "blacktie": 
			$result['backgroundcolor'] = '#333333';
			$result['backgroundurl'] = 'lib/jquery/jquery.s5/images/bg.png';
			$result['headerfontcolor'] = '#a3a3a3';
			$result['headerbackgroundcolor'] = '';
			$result['slidefontcolor'] = '#eeeeee';
			$result['listitemhighlightcolor'] = '#ffeb80';
			break;
		case "trontastic": 
			$result['backgroundcolor'] = '#222222';
			$result['backgroundurl'] = 'lib/jquery/jquery.s5/images/bg.png';
			$result['headerfontcolor'] = '#9fda58';
			$result['headerbackgroundcolor'] = '';
			$result['slidefontcolor'] = '#ffffff';
			$result['listitemhighlightcolor'] = '#f1fbe5';
			break;
		case "swankypurse": 
			$result['backgroundcolor'] = '#261803';
			$result['backgroundurl'] = 'lib/jquery/jquery.s5/images/bg.png';
			$result['headerfontcolor'] = '#eacd86';
			$result['headerbackgroundcolor'] = '';
			$result['slidefontcolor'] = '#efec9f';
			$result['listitemhighlightcolor'] = '#d5ac5d';
			break;
	}
	
	if ($makeJson) return json_encode($result);
	
	return $result;
}

function wikiplugin_slideshow($data, $params) {
	global $dbTiki, $tiki_p_admin, $prefs, $user, $page, $tikilib, $smarty;
	extract ($params,EXTR_SKIP);
	
	if ($theme) {
		$theme = getSlideshowTheme($theme);
		$backgroundcolor = $theme['backgroundcolor'];
		$backgroundurl = (isset($backgroundurl) ? $backgroundurl : $theme['backgroundurl']);
		$headerfontcolor = $theme['headerfontcolor'];
		$headerbackgroundcolor = $theme['headerbackgroundcolor'];
		$slidefontcolor = $theme['slidefontcolor'];
		$listitemhighlightcolor = $theme['listitemhighlightcolor'];
	} else {
		$backgroundcolor = (isset($backgroundcolor) ? $backgroundcolor : '');
		$backgroundurl = (isset($backgroundurl) ? $backgroundurl : '');
		$headerfontcolor = (isset($headerfontcolor) ? $headerfontcolor : '');
		$headerbackgroundcolor = (isset($headerbackgroundcolor) ? $headerbackgroundcolor : '');
		$slidefontcolor = (isset($slidefontcolor) ? $slidefontcolor : '');
		$listitemhighlightcolor = (isset($listitemhighlightcolor) ? $listitemhighlightcolor : '');
	}
	
	$class = (isset($class) ? " $class"  : '');
	$slideduration = (isset($slideseconds) ? $slideseconds : 15) * 1000;
	$textside = (isset($textside) ? $textside : 'left');
	
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
			textSide: '$textside'
		};
	");
	
	return "~np~<div id='' class='tiki_slideshow'>$notesHtml</div>~/np~";
}
